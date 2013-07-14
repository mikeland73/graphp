<?

class Welcome extends GPController {

  public function __construct() {
    parent::__construct();
  }

  public function index($arg1) {
    GP::loadView('welcome_view', ['arg1' => $arg1]);
  }

}
