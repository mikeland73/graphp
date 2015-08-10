<?php

class GPSecurity extends GPObject {

  const EXPIRATION = 86400;

  private static $config;
  private static $token;

  public static function init() {
    self::$config = GPConfig::get();
  }

  public static function getNewCSRFToken() {
    $time = time();
    $id = GPSession::get(GPSession::UID);
    return self::hmacSign($time, self::$config->salt.$id);
  }

  public static function csrf() {
    if (self::$token === null) {
      self::$token = self::getNewCSRFToken();
    }
    return '<input name="csrf" type="hidden" value="'.self::$token.'" />';
  }

  public static function isCSFRTokenValid($token) {
    $timestamp = mb_substr($token, 64, null, '8bit');
    if ($timestamp < time() - self::EXPIRATION) {
      return false;
    }
    $id = GPSession::get(GPSession::UID);
    return self::hmacVerify($token, self::$config->salt.$id);
  }

  public static function hmacSign($message, $key) {
    return hash_hmac('sha256', $message, $key) . $message;
  }

  public static function hmacVerify($bundle, $key) {
    $msgMAC = mb_substr($bundle, 0, 64, '8bit');
    $message = self::hmacGetMessage($bundle);
    // For PHP 5.5 compat
    if (function_exists('hash_equals')) {
      return hash_equals(
        hash_hmac('sha256', $message, $key),
        $msgMAC
      );
    }
    return hash_hmac('sha256', $message, $key) === $msgMAC;
  }

  public static function hmacGetMessage($bundle) {
    return mb_substr($bundle, 64, null, '8bit');
  }

}

GPSecurity::init();
