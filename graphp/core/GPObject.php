<?

class GPObject {

  private static
    $classConstants,
    $classConstantsFlip;

  public function __construct() {

  }

  public static function assertConstValueExists() {
    idxx(self::getClassConstants(), self::getClassConstantsFlip());
  }

  public static function getClassConstants() {
    if (!self::$classConstants) {
      self::initClassConstants();
    }
    return self::$classConstants;
  }

  public static function getClassConstantsFlip() {
    if (!self::$classConstantsFlip) {
      self::initClassConstants();
    }
    return self::$classConstantsFlip;
  }

  private static function initClassConstants() {
    $refl = new ReflectionClass(get_called_class());
    self::$classConstants = $refl->getConstants();
    self::$classConstantsFlip = array_flip(self::$classConstants);
  }

}
