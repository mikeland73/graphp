<?

class GPDatabase extends GPObject {

  use
    GPSingletonTrait;

  private $connection;
  private $guard;
  private $nestedTransactions = 0;

  public function __construct() {
    $this->guard = new AphrontWriteGuard(function() {
      if (idx($_SERVER, 'REQUEST_METHOD') !== 'POST') {
        throw new Exception(
          'You can only write to the database on post requests. If you need to
           make writes on get request,
           use GPDatabase::get()->beginUnguardedWrites()',
          1
        );
      }
      if (!GPSecurity::isCSFRTokenValid(idx($_POST, 'csrf'))) {
        throw new Exception(
          'The request did not have a valid csrf token. This may be an attack or
           you may have forgetten to include it in a post request that does
           writes',
          1
        );
      }
    });
    $config = GPConfig::get('database');
    $this->connection = new AphrontMySQLiDatabaseConnection($config->toArray());
  }

  public function beginUnguardedWrites() {
    AphrontWriteGuard::beginUnguardedWrites();
  }

  public function endUnguardedWrites() {
    AphrontWriteGuard::endUnguardedWrites();
  }

  public function startTransaction() {
    if ($this->nestedTransactions === 0) {
      queryfx($this->connection, 'START TRANSACTION;');
    }
    $this->nestedTransactions++;
  }

  public function commit() {
    $this->nestedTransactions--;
    if ($this->nestedTransactions === 0) {
      queryfx($this->connection, 'COMMIT;');
    }
  }

  public function insertNode(GPNode $node) {
    queryfx(
      $this->connection,
      'INSERT INTO node (type, data) VALUES (%d, %s)',
      $node::getType(),
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

  public function multigetNodeByID(array $ids) {
    if (!$ids) {
      return [];
    }
    return queryfx_all(
      $this->connection,
      'SELECT * FROM node WHERE id IN (%Ld);',
      $ids
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
    $values = [];
    $parts = [];
    foreach ($node->getIndexedData() as $name => $val) {
      $parts[] = '(%d, %d, %s)';
      $values[] = $node->getID();
      $values[] = $node::getDataTypeByName($name)->getIndexedType();
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

  private function getEdgeParts(GPNode $from_node, array $array_of_arrays) {
    $values = [];
    $parts = [];
    foreach ($array_of_arrays as $edge_type => $to_nodes) {
      foreach ($to_nodes as $to_node) {
        $parts[] = '(%d, %d, %d)';
        array_push($values, $from_node->getID(), $to_node->getID(), $edge_type);
      }
    }
    return [$parts, $values];
  }

  public function saveEdges(GPNode $from_node, array $array_of_arrays) {
    list($parts, $values) = $this->getEdgeParts($from_node, $array_of_arrays);
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

  public function deleteEdges(GPNode $from_node, array $array_of_arrays) {
    list($parts, $values) = $this->getEdgeParts($from_node, $array_of_arrays);
    if (!$parts) {
      return;
    }
    vqueryfx(
      $this->connection,
      'DELETE FROM edge WHERE (from_node_id, to_node_id, type) IN ('.
      implode(',', $parts) . ');',
      $values
    );
  }

  public function deleteAllEdges(GPNode $from_node, array $edge_types) {
    $values = [];
    $parts = [];
    foreach ($edge_types as $edge_type) {
      $parts[] = '(%d, %d)';
      array_push($values, $from_node->getID(), $edge_type);
    }
    if (!$parts) {
      return;
    }
    vqueryfx(
      $this->connection,
      'DELETE FROM edge WHERE (from_node_id, type) IN ('.
      implode(',', $parts) . ');',
      $values
    );
  }

  public function getConnectedIDs(array $from_nodes, array $types) {
    if (!$types || !$from_nodes) {
      return [];
    }
    $results = queryfx_all(
      $this->connection,
      'SELECT to_node_id, type FROM edge '.
      'WHERE from_node_id IN (%Ld) AND type IN (%Ld) ORDER BY updated DESC;',
      mpull($from_nodes, 'getID'),
      $types
    );
    $ordered = array_fill_keys($types, []);
    foreach ($results as $result) {
      $ordered[$result['type']] = idx($ordered, $result['type'], []);
      $ordered[$result['type']][$result['to_node_id']] = $result['to_node_id'];
    }
    return $ordered;
  }

  public function getAllByType($type, $limit, $offset) {
    return queryfx_all(
      $this->connection,
      'SELECT * FROM node WHERE type = %d LIMIT %d, %d;',
      $type,
      $offset,
      $limit
    );
  }

  public function getTypeCounts() {
    return queryfx_all(
      $this->connection,
      'SELECT type, COUNT(1) AS count FROM node GROUP BY type;'
    );
  }

  public function deleteNodes(array $nodes) {
    if (!$nodes) {
      return;
    }
    queryfx(
      $this->connection,
      'DELETE FROM node WHERE id IN (%Ld);',
      mpull($nodes, 'getID')
    );
  }

  public function __destruct() {
    $this->guard->isGuardActive() ? $this->guard->dispose() : null;
  }

}
