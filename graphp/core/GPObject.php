<?php

class GPObject {

  private static $classConstants = [];
  private static $classConstantsFlip = [];

  protected function __construct() {

  }

  public static function assertConstValueExists($name) {
    idxx(self::getClassConstantsFlip(), $name);
  }

  public static function getClassConstants() {
    if (!array_key_exists(get_called_class(), self::$classConstants)) {
      self::initClassConstants();
    }
    return self::$classConstants[get_called_class()];
  }

  public static function getClassConstantsFlip() {
    if (!array_key_exists(get_called_class(), self::$classConstantsFlip)) {
      self::initClassConstants();
    }
    return self::$classConstantsFlip[get_called_class()];
  }

  private static function initClassConstants() {
    $refl = new ReflectionClass(get_called_class());
    self::$classConstants[get_called_class()] = $refl->getConstants();
    self::$classConstantsFlip[get_called_class()] =
      array_flip(self::$classConstants[get_called_class()]);
  }

}
