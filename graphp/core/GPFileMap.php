<?php

class GPFileMap extends GPObject {

  private $map = [];
  private $dir;
  private $name;

  public function __construct($dir, $name) {
    $this->dir = $dir;
    $this->name = $name;
    $map = @include $this->buildPath();
    $this->map = $map ?: [];
  }

  public function getPath($file) {
    $file = strtolower($file);
    if (!isset($this->map[$file]) || !file_exists($this->map[$file])) {
      $this->regenMap();
    }

    return idx($this->map, $file);
  }

  public function regenMap() {
    $dir_iter = new RecursiveDirectoryIterator($this->dir);
    $iter = new RecursiveIteratorIterator($dir_iter);
    $this->map = [];
    foreach ($iter as $key => $file) {
      if ($file->getExtension() === 'php') {
        list($name) = explode('.', $file->getFileName());
        $this->map[strtolower($name)] = $key;
      }
    }
    $this->writeMap();
  }

  public function getAllFileNames() {
    $this->regenMap();
    return array_keys($this->map);
  }

  private function writeMap() {
    $map_file = "<?php\nreturn [\n";
    foreach ($this->map as $file => $path) {
      $map_file .= '  '.$file.' => \''.$path."',\n";
    }
    $map_file .= "];\n";
    $file_path = $this->buildPath();
    file_put_contents($file_path, $map_file);
    // TODO this is probably not safe
    @chmod($file_path, 0666);
  }

  private function buildPath() {
    return ROOT_PATH.'maps/'.GPConfig::get()->app_folder.'_'.$this->name;
  }

}
