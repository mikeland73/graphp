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

  private static function getByIndexData($data_type, $data) {
  }

}
