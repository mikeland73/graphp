<?

class GPEdgeType extends GPObject {

  private
    $fromType,
    $toType,
    $name,
    $storageKey;

  public function __construct($to, $name = '', $storage_key = '') {
    $this->toType = $to::getType();
    $this->name = $name ? $name : $to;
    $this->storageKey = $storage_key;
  }

  public function setFromClass($from_class) {
    $this->fromType = $from_class::getType();
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
      return $this->storageKey;
    }
    return $this->fromType.$this->toType.$this->name;
  }

}
