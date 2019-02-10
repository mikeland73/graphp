<?php

class GPFileMap extends GPObject {

  private $map = [];
  private $dir;
  private $name;

  public function __construct($dirs, $name) {
    $this->dirs = make_array($dirs);
    $this->name = $name;
    $map = is_readable($this->buildPath()) ? include $this->buildPath() : null;
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
    $this->map = [];
    foreach ($this->dirs as $dir) {
      $dir_iter = new RecursiveDirectoryIterator($dir);
      $iter = new RecursiveIteratorIterator($dir_iter);

      foreach ($iter as $key => $file) {
        if ($file->getExtension() === 'php') {
          list($name) = explode('.', $file->getFileName());
          $this->map[strtolower($name)] = $key;
        }
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
      $map_file .= '  \''.$file.'\' => \''.$path."',\n";
    }
    $map_file .= "];\n";
    $file_path = $this->buildPath();
    $does_file_exist = file_exists($file_path);
    file_put_contents($file_path, $map_file);
    if (!$does_file_exist) {
      // File was just created, make sure to make it readable
      chmod($file_path, 0666);
    }
  }

  private function buildPath() {
    return '/tmp/maps/'.GPConfig::get()->app_folder.'_'.$this->name;
  }

}
