<?

abstract class GPTest extends PHPUnit_Framework_TestCase {

  public function __construct() {
    parent::__construct();
    GPEnv::isTest();
  }

}
