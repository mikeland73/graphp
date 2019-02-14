<?php

class Welcome extends GPController {

  public function index() {
    GP::view('welcome_view');
  }
}
