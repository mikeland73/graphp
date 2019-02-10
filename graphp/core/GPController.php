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
    $post_json_data = (array) json_decode(file_get_contents('php://input'), true);
    $this->post = new GPRequestData(array_merge_by_keys($_POST, $post_json_data));
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
      !idx(self::$coreHandlers, strtolower($method_name)) &&
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

  /**
   * Override this to limit access to individual methods or the entire controller
   * @param  string
   * @return boolean
   */
  public function isAllowed(string $method): bool {
    return true;
  }
}
