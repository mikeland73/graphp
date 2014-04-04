<?

class STRUtils {

  public static function to64BitInt($string) {
    $hash = substr(md5($string), 16); // truncate to 64 bits
    return (int) base_convert($hash, 16, 10); // base 10
  }

}
