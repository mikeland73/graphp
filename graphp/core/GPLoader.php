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
  ];

  private $lib = [
    'GPSingletonTrait' => true,
  ];

  private $model = [
    'GPDataTypes' => true,
    'GPEdge' => true,
    'GPEdgeMap' => true,
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
      }
    }
  }

  private function GPNodeAutoloader($class_name) {
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

  public static function loadView($view_name, $data = []) {
    $file = ROOT_PATH.'app/views/' . $view_name . '.php';
    if (!file_exists($file)) {
      throw new GPViewNotFoundException();
    }
    require_once $file;
  }
}

class_alias('GPLoader', 'GP');
class GPLoaderException extends Exception {}
class GPControllerNotFoundException extends GPLoaderException {}
class GPViewNotFoundException extends GPLoaderException {}

// To instanciate a new GPLoader we need to call this once.
GP::init();
