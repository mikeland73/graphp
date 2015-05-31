<?php

class GPConfig extends GPObject {

  private static $configs = [];

  private $config;
  private $name;

  public static function get($name = 'general') {
    if (!isset(self::$configs[$name])) {
      self::$configs[$name] = new GPConfig($name);
    }
    return self::$configs[$name];
  }

  protected function __construct($name) {
    $this->name = $name;
    $this->config = require_once ROOT_PATH.'config/' . $name . '.php';
  }

  public function toArray() {
    return $this->config;
  }

  public function __get($name) {
    if (isset($this->config[$name])) {
      return $this->config[$name];
    }
    throw new Exception($name.' is not in '.$this->name.' config' , 1);
  }
}
