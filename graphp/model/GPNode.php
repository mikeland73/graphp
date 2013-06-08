<?

abstract class GPNode extends GPObject {

  private
    $data,
    $data_types = array(),
    $id;

  public function __construct(array $data) {
    $this->data = $data;
  }

  public static function NodeFromArray(array $data) {
    $class = GPNodeMap::getClass($data['type']);
    $node = new $class(json_decode($data['data']));
    $node->id = $data['id'];
    return $node;
  }

  public function setData($key, $value) {
    $type = idxx($this->data_types, $key);
    GPDataTypes::assertValueIsOfType($type, $value);
    $this->data[$key] = $value;
  }

  public function save() {
    // Save node to DB.
    if ($this->id) {
      // existing node, update
    } else {
      // new node, insert.
    }
  }
}
