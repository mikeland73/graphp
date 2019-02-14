<?php

class Welcome extends GPController {

  public function index() {
    GP::view('login_view');
  }
}
