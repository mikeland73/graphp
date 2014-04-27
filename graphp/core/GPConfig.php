<?

class GPConfig extends GPObject {

  private $config;

  public function __construct($name) {
    $this->config = require_once ROOT_PATH.'config/' . $name . '.php';
  }

  public function toArray() {
    return $this->config;
  }

}
