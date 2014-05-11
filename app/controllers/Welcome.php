<?

class Welcome extends GPController {

  public function index($arg1 = 'default') {
    $wallet = Wallet::getOneBySize('small');
    $wallet->addPendingConnectedNodes(
        new GPEdge(Wallet::getType(), User::getType(), 'Owner'),
        [(new User())->save()]
    )->save();

    //$wallet->setConnectedOwner(new User())->save();
    //var_dump($wallet->getSize());
    //$wallet->setSize(123);
    //$user = new User(['name' => 'Mikel']);
    //$user = User::getOneByName('Mikes');
    // $bank_account = (new BankAccount())->save();
    // $user->addBankAccount($bank_account);
    // $user->save();
    // $ids = $user->loadConnectedBankAccount()->getAllConnectedBankAccount();
    //var_dump($ids);
    //$user->setName('Mikes')->save();
    //var_dump($users);
    GP::view('welcome_view', ['arg1' => $arg1]);
  }

}
