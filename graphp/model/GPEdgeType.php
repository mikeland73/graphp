<?php

class GPEdgeType extends GPObject {

  private $fromType;
  private $to;
  private $name;
  private $storageKey;
  private $singleNodeName;
  private $inverse;
  private $type;

  public function __construct($to, $name = '', $storage_key = '') {
    $this->to = $to;
    $this->name = $name ? $name : $to;
    $this->storageKey = $storage_key;
  }

  public function setFromClass($from_class) {
    $this->fromType = $from_class::getType();
  }

  public function getType() {
    if (!$this->type) {
     $this->type = STRUtils::to64BitInt($this->getStorageKey());
    }
    return $this->type;
  }

  public function getName() {
    return strtolower($this->name);
  }

  public function getTo() {
    return $this->to;
  }

  public function getToType() {
    $to = $this->to;
    return $to::getType();
  }

  public function getFromType() {
    return $this->fromType;
  }

  public function setSingleNodeName($name) {
    $this->singleNodeName = $name;
    return $this;
  }

  public function getSingleNodeName() {
    return strtolower($this->singleNodeName);
  }

  public function inverse($inverse) {
    $this->inverse = $inverse;
    $inverse->inverse = $this;
    $inverse->fromType = $this->getToType();
    return $this;
  }

  public function getInverse() {
    return $this->inverse;
  }

  private function getStorageKey() {
    if ($this->storageKey) {
      return $this->storageKey;
    }
    return $this->fromType.$this->getToType().$this->name;
  }
}
