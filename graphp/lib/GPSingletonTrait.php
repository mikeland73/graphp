<?

trait GPSingletonTrait {

  private static $sharedInstance;

  /**
   * Return singleton instance. Creates one if needed.
   * @return this
   */
  public static function sharedInstance() {
    if (!self::$sharedInstance) {
      $reflect  = new ReflectionClass(get_called_class());
      $instance = $reflect->newInstanceArgs(func_get_args());
      self::$sharedInstance = $instance;
    }
    return self::$sharedInstance;
  }

  /**
   * Just an alias for sharedInstance for improved readability
   * @return this
   */
  public static function init() {
    return self::sharedInstance();
  }

  public static function get() {
    return self::sharedInstance();
  }

}
