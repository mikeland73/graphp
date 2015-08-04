<?php

final class GPURLControllerHandler extends GPURIControllerHandler {

  public function handle($method, array $args) {
    return GPConfig::get()->domain.parent::handle($method, $args);
  }

}
