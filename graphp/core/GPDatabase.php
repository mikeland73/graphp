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

  public function __destruct() {
    $this->guard->dispose();
  }

}
