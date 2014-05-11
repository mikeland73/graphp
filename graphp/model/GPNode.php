<?

abstract class GPNode extends GPObject {

  use GPNodeLoader;
  use GPNodeMagicMethods;

  private
    $data,
    $id,
    // array([edge_type] => [array of nodes indexed by id])
    $connectedNodeIDs = [],
    $connectedNodes = [],
    $pendingConnectedNodes = [],
    $pendingRemovalNodes = [],
    $pendingRemovalAllNodes = [];

  protected static
    $data_types = [],
    $edge_types = [],
    $edge_types_by_type = [];

  public function __construct(array $data = []) {
    $this->data = $data;
  }

  public function getID() {
    return (int) $this->id;
  }

  public static function getType() {
    return STRUtils::to64BitInt(static::getStorageKey());
  }

  public static function getStorageKey() {
    return 'node_'.get_called_class();
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

  public function getDataArray() {
    return $this->data;
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
    $db->deleteEdges($this, $this->pendingRemovalNodes);
    $db->deleteAllEdges($this, $this->pendingRemovalAllNodes);
    $this->pendingConnectedNodes = [];
    $this->pendingRemovalNodes = [];
    $this->pendingRemovalAllNodes = [];
    return $this;
  }

  public function addPendingConnectedNodes(GPEdge $edge, array $nodes) {
    return $this->addPendingNodes('pendingConnectedNodes', $edge, $nodes);
  }

  public function addPendingRemovalNodes(GPEdge $edge, array $nodes) {
    return $this->addPendingNodes('pendingRemovalNodes', $edge, $nodes);
  }

  public function addPendingRemovalAllNodes($edge) {
    $this->pendingRemovalAllNodes[$edge->getType()] = $edge->getType();
    return $this;
  }

  private function addPendingNodes($var, GPEdge $edge, array $nodes) {
    assert_equals(
      count($nodes), count(mfilter($nodes, 'getID')),
      'You can\'t add nodes that have not been saved'
    );
    if (!array_key_exists($edge->getType(), $this->$var)) {
      $this->{$var}[$edge->getType()] = [];
    }
    $this->{$var}[$edge->getType()] = array_merge_by_keys(
      $this->{$var}[$edge->getType()],
      mpull($nodes, null, 'getID')
    );
    return $this;
  }

  private function loadConnectedIDs(array $edges) {
    $types = mpull($edges, 'getType');
    $ids = GPDatabase::get()->getConnectedIDs(array($this), $types);
    $this->connectedNodeIDs = array_merge_by_keys(
      $this->connectedNodeIDs,
      $ids
    );
    return $this;
  }

  private function getConnectedIDs(array $edges) {
    $types = mpull($edges, 'getType');
    return array_select_keys($this->connectedNodeIDs, $types);
  }

  public function loadConnectedNodes(array $edges) {
    $ids = $this->loadConnectedIDs($edges)->getConnectedIDs($edges);
    $nodes = self::multiGetByID(array_flatten($ids));
    foreach ($ids as $edge_type => & $ids_for_edge_type) {
      foreach ($ids_for_edge_type as $key => $id) {
        $ids_for_edge_type[$key] = $nodes[$id];
      }
    }
    $this->connectedNodes = array_merge_by_keys($this->connectedNodes, $ids);
    return $this;
  }

  public function getConnectedNodes(array $edges) {
    $types = mpull($edges, 'getType');
    return array_select_keys($this->connectedNodes, $types);
  }

  protected static function getEdgeTypesImpl() {
    return [];
  }

  public function delete() {
    GPDatabase::get()->deleteNodes([$this]);
  }

  final public static function getEdgeTypes() {
    $class = get_called_class();
    if (!isset(static::$edge_types[$class])) {
      $edges = static::getEdgeTypesImpl();
      static::$edge_types[$class] = mpull($edges, null, 'getName');
      static::$edge_types_by_type[$class] = mpull($edges, null, 'getType');
    }
    return static::$edge_types[$class];
  }

  public static function getEdgeType($name) {
    return idxx(static::getEdgeTypes(), $name);
  }

  public static function getEdgeTypeByType($type) {
    $class = get_called_class();
    isset(static::$edge_types_by_type[$class]) ?: static::getEdgeTypes();
    return idxx(static::$edge_types_by_type[$class], $type);
  }
}
