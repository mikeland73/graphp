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

  private function get($key, callable $validator) {
    $value = strip_tags(trim(idx($this->data, $key, '')));
    if ($validator($value)) {
      return $value;
    }
    return null;
  }
}
