<?

class GPController extends GPObject {

  public function __construct() {

  }

  public function __call($method_name, $args) {
    echo 'Method "' . $method_name . '" is not in ' . get_called_class();
  }
}
