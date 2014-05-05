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
    // TODO do validation
    // TODO support directories
    return '/'.strtolower($name).'/'.implode('/', $args);
  }
}
