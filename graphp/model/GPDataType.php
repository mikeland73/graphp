<?

class GPDataType extends GPObject {

  const GP_INT = 'is_int';
  const GP_NODE_ID = 'is_int';
  const GP_FLOAT = 'is_float';
  const GP_NUMBER = 'is_numeric';
  const GP_ARRAY = 'is_array';
  const GP_STRING = 'is_string';
  const GP_BOOL = 'is_bool';
  const GP_ANY = 'is_any';

  private $name;
  private $type;
  private $isIndexed;

  public function __construct(
    $name,
    $type = self::GP_ANY,
    $is_indexed = false
  ) {
    $this->name = mb_strtolower($name);
    $this->type = $type;
    $this->isIndexed = $is_indexed;
  }

  public function assertValueIsOfType($value) {
    self::assertConstValueExists($this->type);
    if ($this->type === self::GP_ANY) {
      return true;
    }
    if (!call_user_func($this->type, $value)) {
      throw new Exception(
        'Value ' . $value . ' is not of type ' . mb_substr($this->type, 3)
      );
    }
    return true;
  }

  public function getName() {
    return $this->name;
  }

  public function getIndexedType() {
    Assert::true($this->isIndexed());
    return STRUtils::to64BitInt($this->name);
  }

  public function isIndexed() {
    return $this->isIndexed;
  }

}
