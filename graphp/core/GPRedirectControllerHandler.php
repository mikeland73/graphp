<?php

class GPRedirectControllerHandler extends GPURIControllerHandler {

  public function handle($method, array $args) {
    GPDatabase::disposeAll();
    $uri = parent::handle($method, $args);
    header('Location: '.$uri, true, 307);
    die();
  }

}
