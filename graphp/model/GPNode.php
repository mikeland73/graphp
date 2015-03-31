<?

abstract class GPNode extends GPObject {

  use GPNodeLoader;
  use GPNodeMagicMethods;
  use GPBatch;

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
    foreach (self::getDataTypes() as $data_type) {
      if (array_key_exists($data_type->getName(), $data)) {
        $data_type->assertValueIsOfType($data[$data_type->getName()]);
      }
    }
    $this->data = [];
    foreach ($data as $key => $value) {
      $this->data[mb_strtolower($key)] = $value;
    }
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
    $data_type = self::getDataTypeByName($key);
    $data_type->assertValueIsOfType($value);
    return $this->setData($key, $value);
  }

  public function setData($key, $value) {
    $this->data[mb_strtolower($key)] = $value;
    return $this;
  }

  public function getDataX($key) {
    Assert::truthy(self::getDataTypeByName($key));
    return $this->getData($key);
  }

  public function getData($key) {
    return idx($this->data, mb_strtolower($key));
  }

  public function getDataArray() {
    return $this->data;
  }

  public function getJSONData() {
    return json_encode($this->data);
  }

  public function getIndexedData() {
    $keys = array_keys(mfilter(self::getDataTypes(), 'isIndexed'));
    return array_select_keys($this->data, $keys);
  }

  public function unsetDataX($key) {
    Assert::truthy(self::getDataTypeByName($key));
    return $this->unsetData($key);
  }

  public function unsetData($key) {
    unset($this->data[mb_strtolower($key)]);
    return $this;
  }

  public function save() {
    $db = GPDatabase::get();
    $db->startTransaction();
    if ($this->id) {
      $db->updateNodeData($this);
    } else {
      $this->id = $db->insertNode($this);
      self::$cache[$this->id] = $this;
    }
    $db->updateNodeIndexedData($this);
    $db->saveEdges($this, $this->pendingConnectedNodes);
    $db->deleteEdges($this, $this->pendingRemovalNodes);
    $db->deleteAllEdges($this, $this->pendingRemovalAllNodes);
    $db->commit();
    $this->pendingConnectedNodes = [];
    $this->pendingRemovalNodes = [];
    $this->pendingRemovalAllNodes = [];
    return $this;
  }

  public function addPendingConnectedNodes(GPEdgeType $edge, array $nodes) {
    return $this->addPendingNodes('pendingConnectedNodes', $edge, $nodes);
  }

  public function addPendingRemovalNodes(GPEdgeType $edge, array $nodes) {
    return $this->addPendingNodes('pendingRemovalNodes', $edge, $nodes);
  }

  public function addPendingRemovalAllNodes($edge) {
    $this->pendingRemovalAllNodes[$edge->getType()] = $edge->getType();
    return $this;
  }

  private function addPendingNodes($var, GPEdgeType $edge, array $nodes) {
    Assert::equals(
      count($nodes),
      count(mfilter($nodes, 'getID')),
      'You can\'t add nodes that have not been saved'
    );
    Assert::allEquals(
      mpull($nodes, 'getType'),
      $edge->getToType(),
      'Trying to add nodes of the wrong type.'
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
    return array_select_keysx($this->connectedNodeIDs, $types);
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
    return array_select_keysx($this->connectedNodes, $types);
  }

  protected static function getEdgeTypesImpl() {
    return [];
  }

  public function delete() {
    unset(self::$cache[$this->getID()]);
    GPDatabase::get()->deleteNodes([$this]);
  }

  final public static function getEdgeTypes() {
    $class = get_called_class();
    if (!isset(static::$edge_types[$class])) {
      $edges = static::getEdgeTypesImpl();
      foreach ($edges as $edge) {
        $edge->setFromClass(get_called_class());
      }
      static::$edge_types[$class] = mpull($edges, null, 'getName');
      static::$edge_types_by_type[$class] = mpull($edges, null, 'getType');
    }
    return static::$edge_types[$class];
  }

  final public static function getEdgeType($name) {
    return idxx(self::getEdgeTypes(), mb_strtolower($name));
  }

  final public static function getEdgeTypeByType($type) {
    $class = get_called_class();
    isset(static::$edge_types_by_type[$class]) ?: self::getEdgeTypes();
    return idxx(static::$edge_types_by_type[$class], $type);
  }

  protected static function getDataTypesImpl() {
    return [];
  }

  final public static function getDataTypes() {
    if (!static::$data_types) {
      static::$data_types = mpull(static::getDataTypesImpl(), null, 'getName');
    }
    return static::$data_types;
  }

  final public static function getDataTypeByName($name) {
    $data_types = self::getDataTypes();
    return idx($data_types, mb_strtolower($name));
  }
}
