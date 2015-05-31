<?php

// We load a couple of things up front. Everything else gets loaded on demand.
class GPException extends Exception {}
require_once ROOT_PATH.'graphp/utils/arrays.php';
require_once ROOT_PATH.'graphp/core/GPObject.php';
require_once ROOT_PATH.'graphp/lib/GPSingletonTrait.php';
require_once ROOT_PATH.'graphp/core/GPFileMap.php';
require_once ROOT_PATH.'third_party/libphutil/src/__phutil_library_init__.php';

class GPLoader extends GPObject {

  private static $maps = [];
  private static $viewData = [];

  public static function init() {
    self::registerGPAutoloader();
    if(!file_exists(ROOT_PATH.'graphp/maps')) {
      throw new GPException(
        'Please create graphp/maps dir and make sure it is writable'
      );
    }
  }

  private static function registerGPAutoloader() {
    spl_autoload_register('GPLoader::GPAutoloader');
    spl_autoload_register('GPLoader::GPNodeAutoloader');
    spl_autoload_register('GPLoader::GPLibraryAutoloader');
    spl_autoload_register('GPLoader::GPControllerAutoloader');
  }

  private static function getMap($path, $name) {
    if (!isset(self::$maps[$name])) {
      self::$maps[$name] = new GPFileMap($path, $name);
    }
    return self::$maps[$name];
  }

  public static function getModelsMap() {
    return self::getMap(ROOT_PATH.'app/models', 'models');
  }

  private static function GPAutoloader($class_name) {
    $path = self::getMap(ROOT_PATH.'graphp', 'graphp')->getPath($class_name);
    if ($path) {
      require_once $path;
    }
  }

  private static function GPNodeAutoloader($class_name) {
    $path = self::getModelsMap()->getPath($class_name);
    if ($path) {
      require_once $path;
    }
  }

  private static function GPLibraryAutoloader($class_name) {
    $map = self::getMap(ROOT_PATH.'app/libraries', 'libraries');
    $path = $map->getPath($class_name);
    if ($path) {
      require_once $path;
    }
  }

  private static function GPControllerAutoloader($class_name) {
    $map = self::getMap(ROOT_PATH.'app/controllers', 'controllers');
    $path = $map->getPath($class_name);
    if ($path) {
      require_once $path;
    }
  }

  public static function view($view_name, array $_data = [], $return = false) {
    if (
      GPConfig::get('database')->disallow_view_db_access &&
      GPDatabase::exists()
    ) {
      GPDatabase::get()->dispose();
    }
    $file = ROOT_PATH.'app/views/' . $view_name . '.php';
    if (!file_exists($file)) {
      throw new GPException('View "'.$view_name.'"" not found');
    }
    $new_data = array_diff_key($_data, self::$viewData);
    $replaced_data = array_intersect_key(self::$viewData, $_data);
    self::$viewData = array_merge_by_keys(self::$viewData, $_data);
    ob_start();
    extract(self::$viewData);
    require $file;
    // Return $viewData to the previous state to avoid view data bleeding.
    self::$viewData = array_merge_by_keys(
      array_diff_key(self::$viewData, $new_data),
      $replaced_data
    );
    if ($return) {
      $buffer = ob_get_contents();
      @ob_end_clean();
      return $buffer;
    }
    ob_end_flush();
  }

  public static function viewWithLayout(
    $view,
    $layout,
    array $data = [],
    array $layout_data = []
  ) {
    if (array_key_exists('content', $layout_data)) {
      throw new GPException('Key: \'content\' cannot be int layout_data');
    }
    $layout_data['content'] = GP::view($view, $data, true);
    GP::view($layout, $layout_data);
  }

  public static function isCLI() {
    return php_sapi_name() === 'cli';
  }

  public static function return404() {
    GPDatabase::get()->dispose();
    http_response_code(404);
    die('404'); // TODO make nice 404 page.
  }

  public static function ajax(array $data) {
    header('Expires: 0');
    header(
      'Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0'
    );
    header('Pragma: no-cache');
    header('Content-type: application/json');
    echo json_encode($data);
  }
}

class_alias('GPLoader', 'GP');

// To instanciate a new GPLoader we need to call this once.
GP::init();
