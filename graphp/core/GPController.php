<?php

class GPController extends GPObject {

  protected $post;
  protected $get;

  private static $coreHandlers = [
    'async' => true,
    'redirect' => true,
    'uri' => true,
    'url' => true,
  ];

  public function init() {
    $this->post = new GPRequestData($_POST);
    $this->get = new GPRequestData($_GET);
  }

  public function __call($method_name, $args) {
    return self::handleStatic($method_name, $args);
  }

  public static function __callStatic($method_name, $args) {
    return self::handleStatic($method_name, $args);
  }

  public function __destruct() {
    GPDatabase::disposeAll();
  }

  private static function handleStatic($method_name, $args) {
    $handler = $method_name.GPConfig::get()->handler_suffix;
    if (
      !idx(self::$coreHandlers, mb_strtolower($method_name)) &&
      is_subclass_of($handler, GPControllerHandler::class)
    ) {
      return $handler::get(get_called_class());
    }
    $core_handler = 'GP'.$method_name.GPConfig::get()->handler_suffix;
    if (is_subclass_of($core_handler, GPControllerHandler::class)) {
      return $core_handler::get(get_called_class());
    }
    if (GPEnv::isDevEnv()) {
      echo 'Method "' . $method_name . '" is not in ' . get_called_class();
    }
    GP::return404();
  }
}
