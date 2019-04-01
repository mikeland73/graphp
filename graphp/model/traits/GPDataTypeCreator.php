<?php

trait GPDataTypeCreator {

  public static function array($name, ...$args) {
    return new GPDataType($name, GPDataType::GP_ARRAY, ...$args);
  }

  public static function string($name, ...$args) {
    return new GPDataType($name, GPDataType::GP_STRING, ...$args);
  }

  public static function bool($name, ...$args) {
    return new GPDataType($name, GPDataType::GP_BOOL, ...$args);
  }

  public static function int($name, ...$args) {
    return new GPDataType($name, GPDataType::GP_INT, ...$args);
  }

  public static function float($name, ...$args) {
    return new GPDataType($name, GPDataType::GP_FLOAT, ...$args);
  }

  public static function number($name, ...$args) {
    return new GPDataType($name, GPDataType::GP_NUMBER, ...$args);
  }

  public static function any($name, ...$args) {
    return new GPDataType($name, GPDataType::GP_ANY, ...$args);
  }
}
