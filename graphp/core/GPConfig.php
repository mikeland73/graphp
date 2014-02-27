<?

class GPConfig extends GPObject {

  private $config;

  public function __construct($name) {
    $this->config = require_once '../config/' . $name . '.php';
  }

  public function toArray() {
    return $this->config;
  }

}
