<?php

class Welcome extends GPController {

  public function index($arg1 = 'default') {
    GPDatabase::get()->beginUnguardedWrites();
    GPDatabase::get()->endUnguardedWrites();
    GP::view('welcome_view', ['arg1' => $arg1]);
  }

}
