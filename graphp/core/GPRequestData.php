<?

class GPRequestData extends GPObject {

  private $data;

  public function __construct(array $data) {
    $this->data = $data;
  }

  public function getInt($key) {
    return $this->get($key, 'is_numeric');
  }

  public function getString($key) {
    return $this->get($key, 'is_string');
  }

  public function getArray($key) {
    return $this->get($key, 'is_array');
  }

  public function getExists($key) {
    return array_key_exists($key, $this->data);
  }

  public function get($key, callable $validator = null) {
    $value = idx($this->data, $key);
    if ($validator === null || $validator($value)) {
      return $value;
    }
    return null;
  }
}
