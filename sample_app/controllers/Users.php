<?php

final class Users extends GPController {

  public function login() {
    $email = $this->post->getString('email');
    $password = $this->post->getString('password');
    $user = User::getOneByEmail($email);
    if (!$user || !password_verify($password, $user->getPassword())) {
      Welcome::redirect()->index();
    }
    GPSession::set('user_id', $user->getID());
    Posts::redirect()->index();
  }

  public function create() {
    $email = $this->post->getString('email');
    $password = $this->post->getString('password');
    $user = User::getOneByEmail($email);
    if ($user) {
      Welcome::redirect()->index();
    }
    $user = (new User())
      ->setEmail($email)
      ->setPassword(password_hash($password, PASSWORD_DEFAULT))
      ->save();
    $this->login();
  }

  public function logout() {
    GPSession::destroy();
    Welcome::redirect()->index();
  }
}
