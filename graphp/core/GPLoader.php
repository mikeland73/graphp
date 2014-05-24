<?

// We load a couple of things up front. Everything else gets loaded on demand.
class GPException extends Exception {}
require_once ROOT_PATH.'graphp/utils/arrays.php';
require_once ROOT_PATH.'graphp/core/GPObject.php';
require_once ROOT_PATH.'graphp/lib/GPSingletonTrait.php';
require_once ROOT_PATH.'graphp/core/GPFileMap.php';
require_once ROOT_PATH.'third_party/libphutil/src/__phutil_library_init__.php';

class GPLoader extends GPObject {

  private static $maps = [];

  public static function init() {
    self::registerGPAutoloader();
  }

  private static function registerGPAutoloader() {
    spl_autoload_register('GPLoader::GPAutoloader');
    spl_autoload_register('GPLoader::GPNodeAutoloader');
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

  private static function GPControllerAutoloader($class_name) {
    $map = self::getMap(ROOT_PATH.'app/controllers', 'controllers');
    $path = $map->getPath($class_name);
    if ($path) {
      require_once $path;
    }
  }

  public static function view($view_name, array $_data = [], $return = false) {
    $file = ROOT_PATH.'app/views/' . $view_name . '.php';
    if (!file_exists($file)) {
      throw new GPException('View "'.$view_name.'"" not found');
    }
    ob_start();
    extract($_data);
    require $file;
    if ($return) {;
      $buffer = ob_get_contents();
      @ob_end_clean();
      return $buffer;
    }
    ob_end_flush();
  }

  public static function viewWithLayout($view, $layout, array $data = []) {
    GP::view($layout, ['content' => GP::view($view, $data, true)]);
  }
}

class_alias('GPLoader', 'GP');

// To instanciate a new GPLoader we need to call this once.
GP::init();
