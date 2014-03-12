<?

class GPNodeMap extends GPObject {

  private static $map = [
    1000 => 'GPEdge',
    1001 => 'Example',
  ];

  private static $inverseMap = [];

  private static function getInverseMap() {
    if (!self::$inverseMap) {
      self::$inverseMap = array_flip(self::$map);
    }
    return self::$inverseMap;
  }

    /**
     * getClass
     *
     * @param mixed $type Description.
     *
     * @access public
     * @static
     *
     * @return mixed Value.
     */
  public static function getClass($type) {
    return idxx(self::$map, $type);
  }

  public static function isNode($node_name) {
    $map = self::getInverseMap();
    return array_key_exists($node_name, $map);
  }
}
