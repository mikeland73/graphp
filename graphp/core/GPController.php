<?

class GPController extends GPObject {

  protected
    $post,
    $get;

  public function init() {
    $this->post = new GPRequestData($_POST);
    $this->get = new GPRequestData($_GET);
  }

  public function __call($method_name, $args) {
    echo 'Method "' . $method_name . '" is not in ' . get_called_class();
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

  public static function redirect($method = '') {
    $uri = static::getURI($method);
    header('Location: '.$uri, true, 302);
    die();
  }
}
