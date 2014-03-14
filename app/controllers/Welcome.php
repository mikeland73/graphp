<?

class Welcome extends GPController {

  public function __construct() {
    parent::__construct();
  }

  public function index($arg1 = 'default') {
    $user = User::getByID(16);
    $user->unsetName()->save();
    GP::loadView('welcome_view', ['arg1' => $arg1]);
  }

}
