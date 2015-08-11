<?php

class GPAsyncControllerHandler extends GPURIControllerHandler {

  public function handle($method, array $args) {
    $uri = parent::handle($method, $args);
    $log = ini_get('error_log') ?: '/dev/null';
    execx('php '.ROOT_PATH.'public/index.php %s >> '.$log.' 2>&1 &', $uri);
  }

  public function __destruct() {
    $this->handle('', []);
  }

}
