<?

class GPController extends GPObject {

  protected
    $post,
    $get;

  public function __construct() {
    $this->post = new GPRequestData($_POST);
    $this->get = new GPRequestData($_GET);
  }

  public function __call($method_name, $args) {
    echo 'Method "' . $method_name . '" is not in ' . get_called_class();
  }

  public static function getURI($method = '') {
    $name = get_called_class();
    $args = func_get_args();
    // TODO support loading controllers (use map)
    // TODO do validation (method should exist and have correct number of args)
    // TODO support directories (in loader class)
    return '/'.strtolower($name).'/'.implode('/', $args);
  }

  public function __destruct() {
    if (class_exists('GPDatabase', false)) {
      GPDatabase::disposeGuardIfNeeded();
    }
  }
}
