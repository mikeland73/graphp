<?

trait GPNodeLoader {

  private static function NodeFromArray(array $data) {
    $node = new static(json_decode($data['data']));
    $node->id = $data['id'];
    return $node;
  }

  public static function getByID($id) {
    $node_data = GPDatabase::get()->getNodeByID($id);
    return self::nodeFromArray($node_data);
  }

  private static function getByIndexData($data_type, $data) {
  }

}
