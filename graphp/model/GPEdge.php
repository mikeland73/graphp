<?

// TODO rename to edgetype
class GPEdge extends GPObject {

  private
    $fromType,
    $toType,
    $name,
    $storageKey;

  public function __construct($from_type, $to_type, $name, $storage_key = '') {
    $this->fromType = $from_type;
    $this->toType = $to_type;
    $this->name = $name;
    $this->storageKey = $storage_key;
  }

  public function getType() {
     return STRUtils::to64BitInt($this->getStorageKey());
  }

  public function getName() {
    return mb_strtolower($this->name);
  }

  public function getToType() {
    return $this->toType;
  }

  public function getFromType() {
    return $this->fromType;
  }

  private function getStorageKey() {
    if ($this->storageKey) {
      $this->storageKey;
    }
    return $this->fromType.$this->toType.$this->name;
  }

}
