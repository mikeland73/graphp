<?

class GPDatabase extends GPObject {

  use
    GPSingletonTrait;

  private
    $connection,
    $guard;

  public function __construct() {
    $this->guard = new AphrontWriteGuard(function() {/*TODO*/});
    $config = new GPConfig('database');
    $this->connection = new AphrontMySQLiDatabaseConnection($config->toArray());
  }

  public function insertNode(GPNode $node) {
    queryfx(
      $this->connection,
      'INSERT INTO node (type, data) VALUES (%d, %s)',
      $node->getType(),
      $node->getJSONData()
    );
    return $this->connection->getInsertID();
  }

  public function updateNodeData(GPNode $node) {
    queryfx(
      $this->connection,
      'UPDATE  node SET data = %s WHERE id = %d',
      $node->getJSONData(),
      $node->getID()
    );
  }

  public function getNodeByID($id) {
    return queryfx_one(
      $this->connection,
      'SELECT * FROM node WHERE id = %d;',
      $id
    );
  }

  public function getNodeIDsByTypeData($type, $data) {
    return ipull(queryfx_all(
      $this->connection,
      'SELECT node_id FROM node_data WHERE type = %d AND data = %s;',
      $type,
      $data
    ), 'node_id');
  }

  public function updateNodeIndexedData(GPNode $node) {
    $values = array();
    $parts = array();
    foreach ($node->getIndexedData() as $name => $val) {
      $parts[] = '(%d, %d, %s)';
      $values[] = $node->getID();
      $values[] = GPDataTypes::getIndexedType($name);
      $values[] = $val;
    }
    if (!$parts) {
      return;
    }
    vqueryfx(
      $this->connection,
      'INSERT INTO node_data (node_id, type, data) VALUES '.
      implode(',', $parts) . ' ON DUPLICATE KEY UPDATE data = VALUES(data);',
      $values
    );
  }

  public function saveEdges(GPNode $from_node, array $array_of_arrays) {
    $values = array();
    $parts = array();
    foreach ($array_of_arrays as $edge_type => $to_nodes) {
      foreach ($to_nodes as $to_node) {
        $parts[] = '(%d, %d, %d)';
        array_push($values, $from_node->getID(), $to_node->getID(), $edge_type);
      }
    }
    if (!$parts) {
      return;
    }
    vqueryfx(
      $this->connection,
      'INSERT IGNORE INTO edge (from_node_id, to_node_id, type) VALUES '.
      implode(',', $parts) . ';',
      $values
    );
  }

  public function getConnectedIDs(array $from_nodes, array $types) {
    if (!$types || !$from_nodes) {
      return array();
    }
    $results = queryfx_all(
      $this->connection,
      'SELECT to_node_id, type FROM edge '.
      'WHERE from_node_id IN (%Ld) AND type IN (%Ld) ORDER BY updated DESC;',
      mpull($from_nodes, 'getID'),
      $types
    );
    $ordered = array();
    foreach ($results as $result) {
      $ordered[$result['type']] = idx($ordered, $result['type'], array());
      $ordered[$result['type']][$result['to_node_id']] = $result['to_node_id'];
    }
    return $ordered;
  }

  public function __destruct() {
    $this->guard->dispose();
  }

}
