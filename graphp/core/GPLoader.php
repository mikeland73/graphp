<?

// We load a couple of things up front. Everything else gets loaded on demand.
require_once '../graphp/core/GPObject.php';
require_once '../graphp/lib/GPSingletonTrait.php';
require_once '../third_party/libphutil/src/__phutil_library_init__.php';

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
    'arrays' => true,
    'assert' => true,
    'Exceptions' => true,
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
        require_once '../graphp/' . $folder . '/' . $class_name . '.php';
      }
    }
  }

  private function GPNodeAutoloader($class_name) {
    if (GPNodeMap::isNode($class_name)) {
      require_once '../app/models/' . $class_name . '.php';
    }
  }

  private function loadUtils() {
    foreach ($this->utils as $file_name => $_) {
      require_once '../graphp/utils/' . $file_name . '.php';
    }
  }

  public static function loadController($controller_name) {
    $file = '../app/controllers/' . $controller_name . '.php';
    if (!file_exists($file)) {
      throw new GPControllerNotFoundException();
    }
    require_once $file;
  }

  public static function loadView($view_name, $data = []) {
    $file = '../app/views/' . $view_name . '.php';
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
