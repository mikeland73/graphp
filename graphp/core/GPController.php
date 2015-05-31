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
    if (GPEnv::isDevEnv()) {
      echo 'Method "' . $method_name . '" is not in ' . get_called_class();
    }
    GP::return404();
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
    return GPConfig::get()->domain.static::getURL($method);
  }

  public static function redirect($method = '') {
    $uri = call_user_func_array(get_called_class().'::getURI', func_get_args());
    header('Location: '.$uri, true, 302);
    die();
  }

  public function __destruct() {
    if (GPDatabase::exists()) {
      GPDatabase::get()->dispose();
    }
  }
}
