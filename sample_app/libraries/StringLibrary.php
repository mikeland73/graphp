<?php

final class StringLibrary extends GPObject {

  public static function truncate($string, $length) {
    if (mb_strlen($string) > $length) {
      $string = mb_substr($string, 0, $length-3).'...';
    }
    return $string;
  }
}
