<?

class Welcome extends GPController {

  public function __construct() {
    parent::__construct();
  }

  public function index($arg1 = 'default') {
    $user = User::getOneByName('Mikes');
    // $bank_account = (new BankAccount())->save();
    // $user->addBankAccount($bank_account);
    // $user->save();
    $ids = $user->loadConnectedBankAccount()->getAllConnectedBankAccount();
    var_dump($ids);
    //$user->setName('Mikes')->save();
    //var_dump($users);
    GP::loadView('welcome_view', ['arg1' => $arg1]);
  }

}
