<?

class Welcome extends GPController {

  public function __construct() {
    parent::__construct();
  }

  public function index($arg1 = 'default') {
    //(new Example())->save();
    //$node = Example::getByID(4);
    //var_dump($node);
    GP::loadView('welcome_view', ['arg1' => $arg1]);
  }

}
