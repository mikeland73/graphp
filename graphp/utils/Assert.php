<?php

class Assert extends GPObject {

  public static function true($var, $message = '') {
    if ($var !== true) {
      throw new GPException(
        $message ?: 'Failed asserting that '.$var.' is true - '
      );
    }
  }

  public static function truthy($var, $message = '') {
    if (!$var) {
      throw new GPException(
        $message ?: 'Failed asserting that '.$var.' is true - '
      );
    }
  }

  public static function false($var, $message = '') {
    if ($var !== false) {
      throw new GPException(
        $message ?: 'Failed asserting that '.$var.' is false - '
      );
    }
  }

  public static function equals($var, $val, $message = '') {
    if ($var !== $val) {
      throw new GPException(
        $message ?: 'Failed asserting that '.$var.' is equal to '.$val.' - '
      );
    }
  }

  public static function inArray($idx, array $array, $message = '') {
    if (!array_key_exists($idx, $array)) {
      throw new GPException($message ?: $idx.' not in '.json_encode($array));
    }
  }

  public static function allEquals(array $vars, $val, $message = '') {
    foreach ($vars as $var) {
      if ($var !== $val) {
        throw new GPException(
          $message ?: 'Failed asserting that '.json_encode($var).' is equal to '.$val.' - '
        );
      }
    }
  }
}
