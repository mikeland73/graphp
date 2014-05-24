<?

class Assert extends GPObject {

  public static function true($var, $message = '') {
    if (!$var) {
      throw new GPException(
        'Failed asserting that '.$var.' is true - '.$message
      );
    }
  }

  public static function false($var, $message = '') {
    if ($var) {
      throw new GPException(
        'Failed asserting that '.$var.' is false - '.$message
      );
    }
  }

  public static function equals($var, $val, $message = '') {
    if ($var !== $val) {
      throw new GPException(
        'Failed asserting that '.$var.' is equal to '.$val.' - '.$message
      );
    }
  }

  public static function inArray($idx, array $array, $message = '') {
    if (!array_key_exists($idx, $array)) {
      throw new GPException($idx.' not in '.json_encode($array));
    }
  }

  public static function allEquals(array $vars, $val, $message = '') {
    foreach ($vars as $var) {
      if ($var !== $val) {
        throw new GPException(
          'Failed asserting that '.$var.' is equal to '.$val.' - '.$message
        );
      }
    }
  }
}
