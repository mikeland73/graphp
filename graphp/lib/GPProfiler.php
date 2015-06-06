<?php

final class GPProfiler {

  private static $enabled = false;
  private static $queryData = [];
  private static $marks = [];

  public static function enable() {
    if (self::$enabled) {
      return;
    }
    self::$enabled = true;
    self::$marks[] = [
      'uri' => idx($_SERVER, 'REQUEST_URI', ''),
      'name' => 'enabled',
      'microtime' => microtime(true),
    ];
    PhutilServiceProfiler::getInstance()->addListener('GPProfiler::format');
  }

  public static function getQueryData() {
    return self::$queryData;
  }

  public static function getMarks() {
    return self::$marks;
  }

  public static function format($type, $id, $data) {
    $data['id'] = $id;
    $data['stage'] = $type;
    self::$queryData[] = $data;
  }

  public static function mark($name = '') {
    if (!self::$enabled) {
      return;
    }
    $name = $name?: 'Mark '.count(self::$marks);
    self::$marks[] = ['name' => $name, 'microtime' => microtime(true)];
    $count = count(self::$marks);
    self::$marks[$count - 1]['duration'] =
        self::$marks[$count - 1]['microtime'] -
        self::$marks[$count - 2]['microtime'];
  }

}
