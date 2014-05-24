<?

class Welcome extends GPController {

  public function index($arg1 = 'default') {
    GPDatabase::get()->beginUnguardedWrites();
    $user = User::getOneByName('Mike');
    $user->addBankAccount((new BankAccount())->save());
    $user->save();
    //$ba = $user->loadBankAccount()->getOneBankAccount();
    GPDatabase::get()->endUnguardedWrites();
    GP::view('welcome_view', ['arg1' => $arg1]);
  }

}
