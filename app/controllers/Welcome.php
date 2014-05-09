<?

class Welcome extends GPController {

  public function index($arg1 = 'default') {
    $user = new User(['name' => 'Mikel']);
    //$user = User::getOneByName('Mikes');
    $bank_account = (new BankAccount())->save();
    $user->addBankAccount($bank_account);
    $user->save();
    $ids = $user->loadConnectedBankAccount()->getAllConnectedBankAccount();
    //var_dump($ids);
    //$user->setName('Mikes')->save();
    //var_dump($users);
    GP::view('welcome_view', ['arg1' => $arg1]);
  }

}
