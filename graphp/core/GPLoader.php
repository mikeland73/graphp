<?

// We load a couple of things up front. Everything else gets loaded on demand.
require_once ROOT_PATH.'graphp/core/GPObject.php';
require_once ROOT_PATH.'graphp/lib/GPSingletonTrait.php';
require_once ROOT_PATH.'third_party/libphutil/src/__phutil_library_init__.php';

class GPLoader extends GPObject {

  use
    GPSingletonTrait;

  private $core = [
    'GPConfig' => true,
    'GPController' => true,
    'GPDatabase' => true,
    'GPLoader' => true,
    'GPObject' => true,
    'GPRouter' => true,
    'GPRequestData' => true,
    'GPSecurity' => true,
    'GPSession' => true,
  ];

  private $lib = [
    'GPSingletonTrait' => true,
  ];

  private $model = [
    'GPDataType' => true,
    'GPEdge' => true,
    'GPNode' => true,
    'GPNodeLoader' => true,
    'GPNodeMagicMethods' => true,
    'GPNodeMap' => true,
  ];

  private $utils = [
    // TODO turn into classes for smarter autoloading
    'arrays' => true,
    'assert' => true,
    'Exceptions' => true,
    'STRUtils' => true,
  ];

  public function __construct() {
    $this->registerGPAutoloader();
    $this->loadUtils();
  }

  private function registerGPAutoloader() {
    spl_autoload_register([$this, 'GPAutoloader']);
    spl_autoload_register([$this, 'GPNodeAutoloader']);
  }

  private function GPAutoloader($class_name) {
    // TODO optimize by saving this into map the first time it is called.
    foreach ($this as $folder => $map) {
      if (array_key_exists($class_name, $map)) {
        require_once
          ROOT_PATH.'graphp/' . $folder . '/' . $class_name . '.php';
        return;
      }
    }
  }

  private function GPNodeAutoloader($class_name) {
    // TODO allow nested dir
    if (GPNodeMap::isNode($class_name)) {
      require_once ROOT_PATH.'app/models/' . $class_name . '.php';
    }
  }

  private function loadUtils() {
    foreach ($this->utils as $file_name => $_) {
      require_once ROOT_PATH.'graphp/utils/' . $file_name . '.php';
    }
  }

  public static function loadController($controller_name) {
    $file = ROOT_PATH.'app/controllers/' . $controller_name . '.php';
    if (!file_exists($file)) {
      throw new GPControllerNotFoundException();
    }
    require_once $file;
  }

  public static function view($view_name, array $_data = [], $return = false) {
    $file = ROOT_PATH.'app/views/' . $view_name . '.php';
    if (!file_exists($file)) {
      throw new GPViewNotFoundException();
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
class GPLoaderException extends Exception {}
class GPControllerNotFoundException extends GPLoaderException {}
class GPViewNotFoundException extends GPLoaderException {}

// To instanciate a new GPLoader we need to call this once.
GP::init();
