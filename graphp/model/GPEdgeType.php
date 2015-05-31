<?php

class GPEdgeType extends GPObject {

  private $fromType;
  private $toType;
  private $name;
  private $storageKey;
  private $singleNodeName;

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

  public function setSingleNodeName($name) {
    $this->singleNodeName = $name;
    return $this;
  }

  public function getSingleNodeName() {
    return mb_strtolower($this->singleNodeName);
  }

  private function getStorageKey() {
    if ($this->storageKey) {
      return $this->storageKey;
    }
    return $this->fromType.$this->toType.$this->name;
  }

}
