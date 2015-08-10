<?php

class GPURIControllerHandler extends GPControllerHandler {

  public function handle($method, array $args) {
    $index = GPConfig::get()->use_index_php ? '/index.php' : '';
    return $index.'/'.strtolower($this->controller).'/'.$method.
      ($args ? '/'.implode('/', $args) : '');
  }

  public function __toString() {
    return $this->handle('', []);
  }

}
