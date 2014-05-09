<?

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
    return $this->name;
  }

  private function getStorageKey() {
    if ($this->storageKey) {
      $this->storageKey;
    }
    return $this->fromType.$this->toType.$this->name;
  }

}
