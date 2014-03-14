<?

abstract class GPNode extends GPObject {

  use GPNodeLoader;
  use GPNodeMagicMethods;

  private
    $data,
    $id,
    // array([edge_type] => [array of nodes indexed by id])
    $connectedNodes = array();

  protected static
    $data_types = array();

  public function __construct(array $data = array()) {
    $this->data = $data;
  }

  public function getID() {
    return $this->id;
  }

  public function getType() {
    return static::TYPE;
  }

  public function setDataX($key, $value) {
    $type_or_array = idxx(static::$data_types, $key);
    $type = is_array($type_or_array) ? idx0($type_or_array) : $type_or_array;
    GPDataTypes::assertValueIsOfType($type, $value);
    return $this->setData($key, $value);
  }

  public function setData($key, $value) {
    $this->data[$key] = $value;
    return $this;
  }

  public function getDataX($key) {
    assert_in_array($key, static::$data_types);
    return $this->getData($key);
  }

  public function getData($key) {
    return idx($this->data, $key);
  }

  public function getJSONData() {
    return json_encode($this->data);
  }

  public function unsetDataX($key) {
    assert_in_array($key, static::$data_types);
    return $this->unsetData($key);
  }

  public function unsetData($key) {
    unset($this->data[$key]);
    return $this;
  }

  public function save() {
    if ($this->id) {
      GPDatabase::get()->updateNodeData($this);
    } else {
      $this->id = GPDatabase::get()->insertNode($this);
    }
    return $this;
  }
}
