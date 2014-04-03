<?

class GPEdgeMap extends GPObject {

  use GPSingletonTrait;

  private $edges;
  private $nameToEdge;

  public function __construct() {
    $this->edges = [
      new GPEdge([
        'from_type' => 1002,
        'to_type' => 1003,
        'name' => 'BankAccount',
      ]),
      //...
    ];
    $this->nameToEdge = mpull($this->edges, null, 'getName');
  }

  public function getEdgeX($name) {
    return idxx($this->nameToEdge, $name);
  }
}
