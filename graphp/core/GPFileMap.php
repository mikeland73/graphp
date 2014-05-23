<?

class GPFileMap extends GPObject {

  private $map = [];
  private $dir;

  public function __construct($dir) {
    $this->dir = $dir;
    $map = @include ROOT_PATH.'graphp/maps/'.md5($dir);
    $this->map = $map ?: [];
  }

  public function getPath($file) {
    if (!isset($this->map[$file])) {
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
        $this->map[$name] = $key;
      }
    }
    $this->writeMap();
  }

  private function writeMap() {
    $map_file = "<?\nreturn [\n";
    foreach ($this->map as $file => $path) {
      $map_file .= '  '.$file.' => \''.$path."',\n";
    }
    $map_file .= "];\n";
    file_put_contents(ROOT_PATH.'graphp/maps/'.md5($this->dir), $map_file);
  }

}
