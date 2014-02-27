<?

class GPDatabase extends GPObject {

  use
    GPSingletonTrait;

  private
    $connection,
    $dbHost,
    $dbName,
    $dbUser,
    $dbPass;

  public function __construct() {
    $config = new GPConfig('database');
    $this->connection = new AphrontMySQLiDatabaseConnection($config->toArray());
  }

}
