<?

class GPFileMap extends GPObject {

  private $map = [];
  private $dir;
  private $name;

  public function __construct($dir, $name) {
    $this->dir = $dir;
    $this->name = $name;
    $map = @include ROOT_PATH.'graphp/maps/'.$this->name;
    $this->map = $map ?: [];
  }

  public function getPath($file) {
    // TODO deal gracefully with files that are moved.
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
    file_put_contents(ROOT_PATH.'graphp/maps/'.$this->name, $map_file);
  }

}
