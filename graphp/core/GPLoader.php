<?

// We ned this here for obovious reasons
require_once '../graphp/lib/GPSingletonTrait.php';
class GPLoaderException extends Exception {}
class GPControllerNotFoundException extends GPLoaderException {}
class GPViewNotFoundException extends GPLoaderException {}

class GPLoader {

  use
    GPSingletonTrait;

  private $core = [
    'GPController' => true,
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
    'GPNode' => true,
    'GPNodeMap' => true,
  ];

  private $utils = [
    'arrays' => true,
  ];

  public function __construct() {
    $this->registerGPAutoloader();
    $this->loadUtils();
  }

  private function registerGPAutoloader() {
    spl_autoload_register([$this, 'GPAutoloader']);
  }

  private function GPAutoloader($class_name) {
    foreach ($this as $folder => $map) {
      if (array_key_exists($class_name, $map)) {
        require_once '../graphp/' . $folder . '/' . $class_name . '.php';
      }
    }
  }

  private function loadUtils() {
    foreach ($this->utils as $file_name => $_) {
      require_once '../graphp/utils/' . $file_name . '.php';
    }
  }

  public function loadController($controller_name) {
    $file = '../app/controllers/' . $controller_name . '.php';
    if (!file_exists($file)) {
      throw new GPControllerNotFoundException();
    }
    require_once $file;
  }

  public function loadView($view_name, $data = []) {
    $file = '../app/views/' . $view_name . '.php';
    if (!file_exists($file)) {
      throw new GPViewNotFoundException();
    }
    require_once $file;
  }
}

// To instanciate a new GPLoader we need to call this once.
GPLoader::init();
