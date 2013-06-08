<?

class GPNodeMap extends GPObject {

  private static $map = [
    1000 => 'GPEdge',
  ];

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
}
