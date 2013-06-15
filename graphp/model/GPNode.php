<?

abstract class GPNode extends GPObject {

  private
    $data,
    $id;

  private static
    $data_types = array();

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
    return $this;
  }

  // Proposed magic API:
  //
  // getX() should only work if x is defined in data_types
  //
  // setY() should only work if y is defined in data_types
  //
  // getConnectedFoo should only work if Foo is the name of a loaded edge
  // (returns one node)
  // getAllConnectedFoo same as getConnectedFoo but returns all (array)
  //
  // addConnectedBar Should only work if Bar is name of edge. Adds a Bar
  // type edge.
  //
  // removeConnected[edge_type]($node) should remove edge to that node
  // removeAllConnected[edge_type] should remove all edges of that type
  //
  // loadConnected[edge_type] called to load edges (and end nodes) of that type
  // loadConnected[edge_type]IDs called to load edges (and end ids) of that type
  //
  // save() must be called for all set() and remove operations to be performed
  //

  public function __call($method, $args) {
    if (substr_compare($method, 'get', 0, 3) === 0) {
      //TODO
    } else if (substr_compare($method, 'set', 0, 3) === 0) {
      // TODO
    } else if (substr_compare($method, 'add', 0, 3) === 0) {
      // TODO
    } else {
      // throw
    }
    return $this;
  }
}
