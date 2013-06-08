<?

class GPDataTypes extends GPObject {

  const GP_INT = 'is_int';
  const GP_FLOAT = 'is_float';
  const GP_NUMBER = 'is_number';
  const GP_ARRAY = 'is_array';
  const GP_STRING = 'is_string';
  const GP_BOOL = 'is_bool';
  const GP_ANY = 'is_any';

  public static function assertValueIsOfType($type, $value) {
    self::assertConstValueExists($type);
    if ($type === self::GP_ANY) {
      return true;
    }
    if (!call_user_func($type, $value)) {
      throw new Exception(
        'Value ' . $value . ' is not of type ' . mb_substr($type, 3)
      );
    }
    return true;
  }

}
