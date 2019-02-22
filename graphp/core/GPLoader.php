<?php

// We load a couple of things up front. Everything else gets loaded on demand.
class GPException extends Exception {}
require_once ROOT_PATH.'graphp/utils/arrays.php';
require_once ROOT_PATH.'graphp/core/GPObject.php';
require_once ROOT_PATH.'graphp/core/GPFileMap.php';
require_once ROOT_PATH.'graphp/core/GPConfig.php';
require_once ROOT_PATH.'third_party/libphutil/src/__phutil_library_init__.php';

class GPLoader extends GPObject {

  private static $map = null;
  private static $viewData = [];
  private static $globalViewData = [];

  public static function init() {
    spl_autoload_register('GPLoader::GPAutoloader');
    if (!is_writable('/tmp/maps')) {
      mkdir('/tmp/maps');
      chmod('/tmp/maps', 0777);
    }
  }

  private static function getMap() {
    if (self::$map === null) {
      $app_folder = GPConfig::get()->app_folder;
      self::$map = new GPFileMap(
        [
          ROOT_PATH.$app_folder.'/models',
          ROOT_PATH.'graphp',
          ROOT_PATH.$app_folder.'/libraries',
          ROOT_PATH.$app_folder.'/controllers',
        ],
        'file_map'
      );
    }
    return self::$map;
  }

  private static function GPAutoloader($class_name) {
    $path = self::getMap()->getPath($class_name);
    if ($path) {
      require_once $path;
    }
  }

  public static function view($view_name, array $_data = [], $return = false) {
    if (GPConfig::get()->disallow_view_db_access) {
      GPDatabase::incrementViewLock();
    }
    $file =
      ROOT_PATH.GPConfig::get()->app_folder.'/views/'.$view_name.'.php';
    if (!file_exists($file)) {
      throw new GPException('View "'.$view_name.'"" not found');
    }
    $new_data = array_diff_key($_data, self::$viewData);
    $replaced_data = array_intersect_key(self::$viewData, $_data);
    self::$viewData = array_merge_by_keys(self::$viewData, $_data);
    ob_start();
    extract(self::$globalViewData);
    extract(self::$viewData);
    require $file;
    // Return $viewData to the previous state to avoid view data bleeding.
    self::$viewData = array_merge_by_keys(
      array_diff_key(self::$viewData, $new_data),
      $replaced_data
    );
    if (GPConfig::get()->disallow_view_db_access) {
      GPDatabase::decrementViewLock();
    }
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

  public static function isAjax() {
    return
      filter_input(INPUT_SERVER, 'HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest';
  }

  public static function isJSONRequest() {
    return idx($_SERVER, 'CONTENT_TYPE') === 'application/json';
  }

  public static function return404() {
    GPDatabase::get()->dispose();
    http_response_code(404);
    $config = GPConfig::get();
    if (GP::isAjax() || GP::isJSONRequest()) {
      GP::ajax(['error_code' => 404, 'error' => 'Resource Not Found']);
    } else if ($config->redirect_404) {
      header('Location: '.$config->redirect_404, true);
    } else if ($config->view_404 && $config->layout_404) {
      GP::viewWithLayout($config->view_404, $config->layout_404);
    } else if ($config->view_404) {
      GP::view($config->view_404);
    } else {
      echo '404';
    }
    die();
  }

  public static function return403() {
    GPDatabase::get()->dispose();
    http_response_code(403);
    if (GP::isAjax() || GP::isJSONRequest()) {
      GP::ajax(['error_code' => 403, 'error' => 'Forbidden']);
    } else {
      echo 'Forbidden';
    }
    die();
  }

  public static function ajax(array $data) {
    header('Expires: 0');
    header(
      'Cache-Control: no-cache, must-revalidate, post-check=0, pre-check=0'
    );
    header('Pragma: no-cache');
    header('Content-type: application/json');
    echo json_encode($data);
    GP::exit();
  }

  public static function exit() {
    GPDatabase::disposeALl();
    exit();
  }

  public static function addGlobal($key, $val) {
    self::$globalViewData[$key] = $val;
  }
}

class_alias('GPLoader', 'GP');

// To instanciate a new GPLoader we need to call this once.
GP::init();
