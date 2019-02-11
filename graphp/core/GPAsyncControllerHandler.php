<?php

class GPAsyncControllerHandler extends GPURIControllerHandler {

  private $handled = false;

  public function handle($method, array $args) {
    $this->handled = true;
    $uri = parent::handle($method, $args);
    $log = ini_get('error_log') ?: '/dev/null';
    execx('php '.ROOT_PATH.'public/index.php %s >> '.$log.' 2>&1 &', $uri);
  }

  public function __destruct() {
    if (!$this->handled) {
      $this->handle('', []);
    }
  }
}
