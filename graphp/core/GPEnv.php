<?php

final class GPEnv extends GPObject {

  private static $isTestEnv = false;

  public static function isTest() {
    self::$isTestEnv = true;
  }

  public static function isTestEnv() {
    return self::$isTestEnv;
  }

  public static function assertTestEnv() {
    Assert::true(self::isTestEnv());
  }

  public static function isDevEnv() {
    return GPConfig::get()->is_dev;
  }
}
