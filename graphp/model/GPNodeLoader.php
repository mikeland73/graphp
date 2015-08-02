<?php

trait GPNodeLoader {

  private static $cache = array();

  public static function createFromType($type, array $data = array()) {
    $class = GPNodeMap::getClass($type);
    return new $class($data);
  }

  private static function NodeFromArray(array $data) {
    $class = GPNodeMap::getClass($data['type']);
    $node = new $class(json_decode($data['data'], true));
    $node->id = $data['id'];
    $node->updated = $data['updated'];
    return $node;
  }

  public static function getByID($id) {
    if (!array_key_exists($id, self::$cache)) {
      $node_data = GPDatabase::get()->getNodeByID($id);
      if ($node_data === null) {
        return null;
      }
      self::$cache[$id] = self::nodeFromArray($node_data);
    }
    return self::$cache[$id];
  }

  public static function multiGetByID(array $ids) {
    $ids = key_by_value($ids);
    $to_fetch = array_diff_key($ids, self::$cache);
    $node_datas = GPDatabase::get()->multigetNodeByID($to_fetch);
    foreach ($node_datas as $node_data) {
      self::$cache[$node_data['id']] = self::nodeFromArray($node_data);
    }
    return array_select_keys(self::$cache, $ids);
  }

  public static function __callStatic($name, array $arguments) {
    $only_one = false;
    $len = mb_strlen($name);
    if (
      strpos($name, 'getBy') === 0  &&
      strpos($name, 'Range') === ($len - 5)
    ) {
      $type_name = mb_strtolower(mb_substr($name, 5, $len - 10));
      $range = true;
    } else if (substr_compare($name, 'getBy', 0, 5) === 0) {
      $type_name = mb_strtolower(mb_substr($name, 5));
    } else if (substr_compare($name, 'getOneBy', 0, 8) === 0) {
      $type_name = mb_strtolower(mb_substr($name, 8));
      $only_one = true;
    }
    if (isset($type_name)) {
      $class = get_called_class();
      $data_type = static::getDataTypeByName($type_name);
      Assert::truthy($data_type, $name.' is not a method on '.$class);
      Assert::truthy(
        isset($range) ? count($arguments) >= 2 : count($arguments) === 1,
        GPErrorText::wrongArgs($class, $name, 1, count($arguments))
      );
      $arg = idx0($arguments);
      foreach (make_array($arg) as $val) {
        $data_type->assertValueIsOfType($val);
      }
      if (isset($range)) {
        array_unshift($arguments, $data_type->getIndexedType());
        $results = self::getByIndexDataRange($arguments);
      } else {
        $results = self::getByIndexData($data_type->getIndexedType(), $arg);
      }
      return $only_one ? idx0($results) : $results;
    }
    throw new GPException('Method '.$name.' not found in '.get_called_class());
  }

  public static function getAll($limit = GPDatabase::LIMIT, $offset = 0) {
    $node_datas =
      GPDatabase::get()->getAllByType(static::getType(), $limit, $offset);
    foreach ($node_datas as $node_data) {
      if (!isset(self::$cache[$node_data['id']])) {
        self::$cache[$node_data['id']] = self::nodeFromArray($node_data);
      }
    }
    return array_select_keys(self::$cache, ipull($node_datas, 'id'));
  }

  public static function clearCache() {
    self::$cache = [];
  }

  private static function getByIndexData($data_type, $data) {
    $db = GPDatabase::get();
    $node_ids = $db->getNodeIDsByTypeData($data_type, make_array($data));
    return self::multiGetByID($node_ids);
  }

  private static function getByIndexDataRange($args) {
    $ids = GPDatabase::get()->getNodeIDsByTypeDataRange(
      $args[0],
      $args[1],
      $args[2],
      idx($args, 3, GPDatabase::LIMIT),
      idx($args, 4, 0));
    return self::multiGetByID($ids);
  }

}
