<?

class GPDatabase extends GPObject {

  use
    GPSingletonTrait;

  private
    $dbHost,
    $dbName,
    $dbUser,
    $dbPass;


  public function __construct() {
    $config = new GPConfig('database');
  }

}
