<?

class GPSession extends GPObject {

  const UID = 'uid';

  private static $config;
  private static $session;

  public static function init() {
    self::$config = GPConfig::get();
    $json_with_hash = idx($_COOKIE, self::$config->cookie_name, '');
    $json = '[]';
    if ($json_with_hash) {
      $json = mb_substr($json_with_hash, 0, -40);
      $hash = mb_substr($json_with_hash, -40);
      if (sha1($json.self::$config->salt) !== $hash) {
        throw new Exception('Cookie hash missmatch. Possible attack', 1);
      }
    }
    self::$session = json_decode($json, true);
    if (!self::get(self::UID)) {
      self::set(self::UID, base64_encode(microtime().mt_rand()));
    }
  }

  public static function get($key, $default = null) {
    return idx(self::$session, $key, $default);
  }

  public static function set($key, $val) {
    self::$session[$key] = $val;
    self::updateCookie();
  }

  public static function delete($key) {
    unset(self::$session[$key]);
    self::updateCookie();
  }

  public static function destroy() {
    setcookie(
      self::$config->cookie_name,
      '',
      0,
      '/',
      self::$config->cookie_domain
    );
  } 

  private static function updateCookie() {
    $json = json_encode(self::$session);
    $json_with_hash = $json.sha1($json.self::$config->salt);
    if (strlen($json_with_hash) > 4093) {
      throw new Exception(
        'Your session cookie is too large. That may break in some browsers.
         Consider storing large info in the DB.',
        1
      );
    }
    $result = setcookie(
      self::$config->cookie_name,
      $json_with_hash,
      time() + self::$config->cookie_exp,
      '/',
      self::$config->cookie_domain
    );
    if (!$result) {
      throw new Exception(
        'Error setting session cookie, make sure not to print any content
         prior to setting cookie',
        1
      );

    }
  }

}

GPSession::init();
