<?php

class GPController extends GPObject {

  protected
    $post,
    $get;

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

  public static function getURI($method = '') {
    $name = get_called_class();
    $args = func_get_args();
    if ($method) {
      if (
        !method_exists($name, $method) ||
        !(new ReflectionMethod($name, $method))->isPublic()
      ) {
        throw new GPException($name.' does not have a public method '.$method);
      }
    }
    $index = GPConfig::get()->use_index_php ? '/index.php' : '';
    return $index . '/'.strtolower($name).'/'.implode('/', $args);
  }

  public static function getURL($method = '') {
    return GPConfig::get()->domain.static::getURI($method);
  }

  public static function runAsync($method='') {
    $uri = call_user_func_array(get_called_class().'::getURI', func_get_args());
    $log = ini_get('error_log') ?: '/dev/null';
    execx('php '.ROOT_PATH.'public/index.php %s >> '.$log.' 2>&1 &', $uri);
  }

  public static function isActive($method = 'index') {
    $class = get_called_class();
    return
      GPRouter::getController() instanceof $class &&
      strcasecmp(GPRouter::getMethod(), $method) === 0;
  }

  public function __destruct() {
    GPDatabase::disposeAll();
  }

  private static function handleStatic($method_name, $args) {
    $handler = $method_name.GPConfig::get()->handler_suffix;
    if (is_subclass_of($handler, GPControllerHandler::class)) {
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
