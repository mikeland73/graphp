<?php

final class GPProfiler {

  private $data = [];

  public static function enable() {
    $profiler = new GPProfiler();
    PhutilServiceProfiler::getInstance()->addListener([$profiler, 'format']);
  }

  public function getData() {
    return $this->data;
  }

  public function format($type, $id, $data) {
    $data['id'] = $id;
    $data['stage'] = $type;
    $this->data[] = $data;
  }

  public function __destruct() {
    var_dump($this->data);
  }
}
