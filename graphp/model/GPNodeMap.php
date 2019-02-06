<?php

class GPNodeMap extends GPObject {

  private static $inverseMap = [];

  private static $map = [];

  private static function getMap() {
    if (!self::$map) {
      self::$map = is_readable(self::buildPath()) ? include self::buildPath() : null;
    }
    return self::$map ?: [];
  }

  private static function getInverseMap() {
    if (!self::$inverseMap) {
      self::$inverseMap = array_flip(self::getMap());
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
    if (!idx(self::getMap(), $type)) {
      self::regenMap();
    }
    return idxx(self::getMap(), $type);
  }

  public static function isNode($node_name) {
    $map = self::getInverseMap();
    if (!idx($map, $node_name)) {
      self::regenMap();
      $map = self::getInverseMap();
    }
    return array_key_exists($node_name, $map);
  }

  public static function regenAndGetAllTypes() {
    self::regenMap();
    return self::getMap();
  }

  public static function addToMapForTest($class) {
    GPEnv::assertTestEnv();
    self::$map[$class::getType()] = $class;
  }

  private static function regenMap() {
    $app_folder = GPConfig::get()->app_folder;
    self::$map = [];
    self::$inverseMap = [];
    $file = "<?php\nreturn [\n";
    $model_map = new GPFileMap(ROOT_PATH.$app_folder.'/models', 'models');
    foreach ($model_map->getAllFileNames() as $class) {
      $file .= '  '.$class::getType().' => \''.$class."',\n";
      self::$map[$class::getType()] = $class;
      self::$inverseMap[$class] = $class::getType();
    }
    $file .= "];\n";
    $file_path = self::buildPath();
    $does_file_exist = file_exists($file_path);
    file_put_contents($file_path, $file);
    if (!$does_file_exist) {
      // File was just created, make sure to make it readable
      chmod($file_path, 0666);
    }
  }

  private static function buildPath() {
    return '/tmp/maps/'.GPConfig::get()->app_folder.'_node';
  }
}
