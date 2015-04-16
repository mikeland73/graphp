<?

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
    return sha1($time.self::$config->salt.$id).$time;
  }

  public static function csrf() {
    if (self::$token === null) {
      self::$token = self::getNewCSRFToken();
    }
    return '<input name="csrf" type="hidden" value="'.self::$token.'" />';
  }

  public static function isCSFRTokenValid($token) {
    $hash = mb_substr($token, 0, 40);
    $timestamp = mb_substr($token, 40);
    if ($timestamp < time() - self::EXPIRATION) {
      return false;
    }
    $id = GPSession::get(GPSession::UID);
    return sha1($timestamp.self::$config->salt.$id) === $hash;
  }

}

GPSecurity::init();
