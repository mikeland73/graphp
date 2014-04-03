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

  public function saveEdges(array $array_of_arrays) {

  }

  public function __destruct() {
    $this->guard->dispose();
  }

}
