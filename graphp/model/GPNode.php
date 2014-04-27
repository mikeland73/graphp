<?

abstract class GPNode extends GPObject {

  use GPNodeLoader;
  use GPNodeMagicMethods;

  private
    $data,
    $id,
    // array([edge_type] => [array of nodes indexed by id])
    $connectedNodeIDs = array(),
    $connectedNodes = array(),
    $pendingConnectedNodes = array();

  protected static
    $data_types = array();

  public function __construct(array $data = array()) {
    $this->data = $data;
  }

  public function getID() {
    return (int) $this->id;
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

  public function getIndexedData() {
    $keys = array_keys(array_filter(
      static::$data_types,
      function($x) {
        return is_array($x) && idx($x, 1) === GPDataTypes::INDEXED;
      }
    ));
    return array_select_keys($this->data, $keys);
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
    // TODO transaction
    $db = GPDatabase::get();
    if ($this->id) {
      $db->updateNodeData($this);
    } else {
      $this->id = $db->insertNode($this);
    }
    $db->updateNodeIndexedData($this);
    $db->saveEdges($this, $this->pendingConnectedNodes);
    $this->pendingConnectedNodes = array();
    return $this;
  }

  private function addPendingConnectedNodes(GPEdge $edge, array $nodes) {
    assert_equals(
      count($nodes), count(mfilter($nodes, 'getID')),
      'You can\'t add nodes that have not been saved'
    );
    if (!array_key_exists($edge->getEdgeType(), $this->pendingConnectedNodes)) {
      $this->pendingConnectedNodes[$edge->getEdgeType()] = array();
    }
    $this->pendingConnectedNodes[$edge->getEdgeType()] = array_merge_by_keys(
      $this->pendingConnectedNodes[$edge->getEdgeType()],
      mpull($nodes, null, 'getID')
    );
  }

  private function loadConnectedIDs(array $edges) {
    $types = mpull($edges, 'getEdgeType');
    $ids = GPDatabase::get()->getConnectedIDs(array($this), $types);
    $this->connectedNodeIDs = array_merge_by_keys(
      $this->connectedNodeIDs,
      $ids
    );
    return $this;
  }

  private function getConnectedIDs(array $edges) {
    $types = mpull($edges, 'getEdgeType');
    return array_select_keys($this->connectedNodeIDs, $types);
  }
}
