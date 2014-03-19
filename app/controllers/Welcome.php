<?

class Welcome extends GPController {

  public function __construct() {
    parent::__construct();
  }

  public function index($arg1 = 'default') {
    $users = User::getByName('Mikes');
    //$user->setName('Mikes')->save();
    //var_dump($users);
    GP::loadView('welcome_view', ['arg1' => $arg1]);
  }

}
