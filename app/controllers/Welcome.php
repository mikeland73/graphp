<?

class Welcome extends GPController {

  public function index($arg1 = 'default') {
    //GPDatabase::get()->beginUnguardedWrites();
    $user = User::getOneByName('Mike');
    $ba = $user->loadBankAccount()->getOneBankAccount();
    GP::view('welcome_view', ['arg1' => $arg1]);
  }

}
