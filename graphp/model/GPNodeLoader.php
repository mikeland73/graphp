<?

trait GPNodeLoader {

  private static $cache = array();

  private static function NodeFromArray(array $data) {
    $node = new static(json_decode($data['data'], true));
    $node->id = $data['id'];
    return $node;
  }

  public static function getByID($id) {
    if (!array_key_exists($id, self::$cache)) {
      $node_data = GPDatabase::get()->getNodeByID($id);
      self::$cache[$id] = self::nodeFromArray($node_data);
    }
    return self::$cache[$id];
  }

  public static function multiGetByID(array $ids) {
    if (!$ids) {
      return array();
    }
    // TODO
    return array(self::getByID(idx0($ids)));
  }

  public static function __callStatic($name, array $arguments) {
    if (substr_compare($name, 'getBy', 0, 5) === 0) {
      $type_name = mb_strtolower(mb_substr($name, 5));
      assert_in_array($type_name, static::$data_types, 'GPBadArgException');
      assert_equals(count($arguments), 1, 'GPBadArgException');
      return self::getByIndexData(
        GPDataTypes::getIndexedType($type_name),
        head($arguments)
      );
    }
    throw new GPBadMethodCallException();
  }

  private static function getByIndexData($data_type, $data) {
    $db = GPDatabase::get();
    $node_ids = $db->getNodeIDsByTypeData($data_type, $data);
    return self::multiGetByID($node_ids);
  }

}
