<?php

class GPRequestData extends GPObject {

  private $data;

  public function __construct(array $data) {
    $this->data = $data;
  }

  public function getData() {
    return $this->data;
  }

  public function getInt($key, $default = null) {
    $val = $this->get($key, 'is_numeric');
    return  $val !== null ? (int) $val : $default;
  }

  public function getString($key, $default = null) {
    $val = $this->get($key, 'is_string');
    return  $val !== null ? $val : $default;
  }

  public function getArray($key, $default = null) {
    $val = $this->get($key, 'is_array');
    return  $val !== null ? $val : $default;
  }

  public function getExists($key) {
    return array_key_exists($key, $this->data);
  }

  public function get($key, callable $validator = null) {
    $value = idx($this->data, $key);
    if ($validator === null || $validator($value)) {
      return $value;
    }
    return null;
  }

  public function serialize() {
    return json_encode($this->data);
  }
}
