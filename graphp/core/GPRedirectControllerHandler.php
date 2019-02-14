<?php

class GPRedirectControllerHandler extends GPURIControllerHandler {

  private $handled = false;

  public function handle($method, array $args) {
    $this->handled = true;
    // The autoloader will not work if PHP is shutting down.
    if (class_exists('GPDatabase', false)) {
      GPDatabase::disposeAll();
    }
    $uri = parent::handle($method, $args);
    header('Location: '.$uri, true, 307);
    die();
  }

  public function disableDestructRedirect() {
    $this->handled = true;
  }

  /**
   * This will redirect when the handler is destroyed allowing for shorter code
   * like:
   * MyController::redirect();
   * instead of
   * MyController::redirect()->index();
   */
  public function __destruct() {
    if (!$this->handled) {
      $this->handle('', []);
    }
  }
}
