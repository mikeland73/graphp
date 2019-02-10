<?php

class GPDatabase extends GPObject {

  const LIMIT = 100;

  private static $dbs = [];
  private $connection;
  private $guard;
  private $nestedTransactions = 0;

  private static $viewLock = 0;
  private static $skipWriteGuard = false;

  public static function get($name = 'database') {
    if (!isset(self::$dbs[$name])) {
      self::$dbs[$name] = new GPDatabase($name);
    }
    return self::$dbs[$name];
  }

  public function __construct($config_name) {
    $this->guard = new AphrontWriteGuard(function() {
      if (GP::isCLI() || self::$skipWriteGuard) {
        return;
      }
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
    $config = GPConfig::get($config_name);
    $this->connection = new AphrontMySQLiDatabaseConnection($config->toArray());
  }

  public function getConnection() {
    Assert::equals(
      self::$viewLock,
      0,
      'Tried to access DB while view lock is on'
    );
    Assert::truthy($this->connection, 'DB Connection no longer exists');
    return $this->connection;
  }

  public function beginUnguardedWrites() {
    AphrontWriteGuard::beginUnguardedWrites();
  }

  public function endUnguardedWrites() {
    AphrontWriteGuard::endUnguardedWrites();
  }

  public function skipWriteGuard() {
    self::$skipWriteGuard = true;
  }

  public function startTransaction() {
    if ($this->nestedTransactions === 0) {
      queryfx($this->getConnection(), 'START TRANSACTION;');
    }
    $this->nestedTransactions++;
  }

  public function commit() {
    $this->nestedTransactions--;
    if ($this->nestedTransactions === 0) {
      queryfx($this->getConnection(), 'COMMIT;');
    }
  }

  public function insertNode(GPNode $node) {
    queryfx(
      $this->getConnection(),
      'INSERT INTO node (type, data) VALUES (%d, %s)',
      $node::getType(),
      $node->getJSONData()
    );
    return $this->getConnection()->getInsertID();
  }

  public function updateNodeData(GPNode $node) {
    queryfx(
      $this->getConnection(),
      'UPDATE  node SET data = %s WHERE id = %d',
      $node->getJSONData(),
      $node->getID()
    );
  }

  public function getNodeByID($id) {
    return queryfx_one(
      $this->getConnection(),
      'SELECT * FROM node WHERE id = %d;',
      $id
    );
  }

  public function getNodeByIDType($id, $type) {
    return queryfx_one(
      $this->getConnection(),
      'SELECT * FROM node WHERE id = %d AND type = %d;',
      $id,
      $type
    );
  }

  public function multigetNodeByIDType(array $ids, $type = null) {
    if (!$ids) {
      return [];
    }
    $type_fragment = $type ? 'AND type = %d;' : ';';
    $args = array_filter([$ids, $type]);
    return queryfx_all(
      $this->getConnection(),
      'SELECT * FROM node WHERE id IN (%Ld) '.$type_fragment,
      ...$args
    );
  }

  public function getNodeIDsByTypeData($type, array $data) {
    if (!$data) {
      return [];
    }
    return ipull(queryfx_all(
      $this->getConnection(),
      'SELECT node_id FROM node_data WHERE type = %d AND data IN (%Ls);',
      $type,
      $data
    ), 'node_id');
  }

  public function getNodeIDsByTypeDataRange(
    $type,
    $start,
    $end,
    $limit,
    $offset
  ) {
    return ipull(queryfx_all(
      $this->getConnection(),
      'SELECT node_id FROM node_data WHERE '.
        'type = %d AND data >= %s AND data <= %s ORDER BY updated ASC LIMIT %d, %d;',
      $type,
      $start,
      $end,
      $offset,
      $limit
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
    $this->startTransaction();
    $result = queryfx(
      $this->getConnection(),
      'DELETE FROM node_data WHERE node_id = %d',
      $node->getID()
    );
    if ($parts) {
      queryfx(
        $this->getConnection(),
        'INSERT INTO node_data (node_id, type, data) VALUES '.
        implode(',', $parts) . ' ON DUPLICATE KEY UPDATE data = VALUES(data);',
        ...$values
      );
    }
    $this->commit();
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
    queryfx(
      $this->getConnection(),
      'INSERT IGNORE INTO edge (from_node_id, to_node_id, type) VALUES '.
      implode(',', $parts) . ';',
      ...$values
    );
  }

  public function deleteEdges(GPNode $from_node, array $array_of_arrays) {
    list($parts, $values) = $this->getEdgeParts($from_node, $array_of_arrays);
    if (!$parts) {
      return;
    }
    queryfx(
      $this->getConnection(),
      'DELETE FROM edge WHERE (from_node_id, to_node_id, type) IN ('.
      implode(',', $parts) . ');',
      ...$values
    );
  }

  public function deleteAllEdges(GPNode $node, array $edge_types) {
    return $this->deleteAllEdgesInternal($node, $edge_types, 'from_node_id');
  }

  public function deleteAllInverseEdges(GPNode $node, array $edge_types) {
    return $this->deleteAllEdgesInternal($node, $edge_types, 'to_node_id');
  }

  private function deleteAllEdgesInternal(
    GPNode $node,
    array $edge_types,
    $col
  ) {
    $values = [];
    $parts = [];
    foreach ($edge_types as $edge_type) {
      $parts[] = '(%d, %d)';
      array_push($values, $node->getID(), $edge_type);
    }
    if (!$parts) {
      return;
    }
    queryfx(
      $this->getConnection(),
      'DELETE FROM edge WHERE ('.$col.', type) IN ('.
      implode(',', $parts) . ');',
      ...$values
    );
  }

  public function getConnectedIDs(
    GPNode $from_node,
    array $types,
    $limit = null,
    $offset = 0
  ) {
    return idx(
      $this->multiGetConnectedIDs([$from_node], $types, $limit, $offset),
      $from_node->getID(),
      array_fill_keys($types, [])
    );
  }

  public function multiGetConnectedIDs(
    array $from_nodes,
    array $types,
    $limit = null,
    $offset = 0
  ) {
    if (!$types || !$from_nodes) {
      return [];
    }
    $args = [mpull($from_nodes, 'getID'), $types];
    if ($limit !== null) {
      $args[] = $offset;
      $args[] = $limit;
    }
    $results = queryfx_all(
      $this->getConnection(),
      'SELECT from_node_id, to_node_id, type FROM edge '.
      'WHERE from_node_id IN (%Ld) AND type IN (%Ld) ORDER BY updated DESC'.
      ($limit === null ? '' : ' LIMIT %d, %d').';',
      ...$args
    );
    $ordered = [];
    foreach ($results as $result) {
      if (!array_key_exists($result['from_node_id'], $ordered)) {
        $ordered[$result['from_node_id']] = array_fill_keys($types, []);
      }
      $ordered[$result['from_node_id']][$result['type']][$result['to_node_id']]
        = $result['to_node_id'];
    }
    return $ordered;
  }

  public function getConnectedNodeCount(array $nodes, array $edges) {
    $results = queryfx_all(
      $this->getConnection(),
      'SELECT from_node_id, type, count(1) as c FROM edge '.
      'WHERE from_node_id IN (%Ld) AND type IN (%Ld) group by from_node_id, '.
        'type;',
      mpull($nodes, 'getID'),
      mpull($edges, 'getType')
    );
    return igroup($results, 'from_node_id');
  }

  public function getAllByType($type, $limit, $offset) {
    return queryfx_all(
      $this->getConnection(),
      'SELECT * FROM node WHERE type = %d ORDER BY updated DESC LIMIT %d, %d;',
      $type,
      $offset,
      $limit
    );
  }

  public function getTypeCounts() {
    return queryfx_all(
      $this->getConnection(),
      'SELECT type, COUNT(1) AS count FROM node GROUP BY type;'
    );
  }

  public function deleteNodes(array $nodes) {
    if (!$nodes) {
      return;
    }
    queryfx(
      $this->getConnection(),
      'DELETE FROM node WHERE id IN (%Ld);',
      mpull($nodes, 'getID')
    );
  }

  public static function incrementViewLock() {
    self::$viewLock++;
  }

  public static function decrementViewLock() {
    self::$viewLock--;
  }

  public function dispose() {
    if ($this->guard->isGuardActive()) {
      $this->guard->dispose();
    }
    if ($this->connection) {
      $this->connection->close();
      $this->connection = null;
    }
  }

  public static function disposeAll() {
    foreach (self::$dbs as $db) {
      $db->dispose();
    }
  }

  public function __destruct() {
    $this->dispose();
  }

}
