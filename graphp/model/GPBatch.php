<?

final class GPBatch extends GPObject {

  private $nodes = [];
  private $classes = [];
  private $lazy;
  private $lazyEdges = [];
  private $lazyForce = true;

  public function __construct(array $nodes, $lazy) {
    $this->nodes = $nodes;
    $this->classes = array_unique(array_map(
      function($node) { return get_class($node); },
      $nodes
    ));
    $this->lazy = $lazy;
  }

  public function delete() {
    GPNode::batchDelete($this->nodes);
  }

  public function save() {
    GPNode::batchSave($this->nodes);
    return $this;
  }

  public function __call($method, $args) {

    if (substr_compare($method, 'forceLoad', 0, 9) === 0) {
      if ($this->lazy) {
        $this->lazyForce = true;
      }
      return $this->handleLoad(mb_substr($method, 5), $args, true);
    }

    if (substr_compare($method, 'load', 0, 4) === 0) {
      return $this->handleLoad($method, $args);
    }

    throw new GPException(
      'Method '.$method.' not found in '.get_called_class()
    );
  }

  public function load() {
    Assert::true($this->lazy, 'Cannot call load on non lazy batch loader');
    GPNode::batchLoadConnectedNodes(
      $this->nodes,
      $this->lazyEdges,
      $this->lazyForce
    );
    $this->lazyEdges = [];
    $this->lazyForce = false;
    return;
  }

  private function handleLoad($method, $args, $force = false) {
    if (substr_compare($method, 'IDs', -3) === 0) {
      if ($this->lazy) {
        throw new GPException('Lazy ID loading is not supported');
      } else {
        GPNode::batchLoadConnectedNodes(
          $this->nodes,
          $this->getEdges(mb_substr($method, 4, -3)),
          $force,
          true
        );
      }
    } else {
      if ($this->lazy) {
        $this->lazyEdges +=
          mpull($this->getEdges(mb_substr($method, 4)), null, 'getType');
      } else {
        GPNode::batchLoadConnectedNodes(
          $this->nodes,
          $this->getEdges(mb_substr($method, 4)),
          $force
        );
      }
    }
    return $this;
  }

  private function getEdges($edge_name) {
    return array_filter(array_map(
      function($class) use ($edge_name) {
        return $class::isEdgeType($edge_name) ?
          $class::getEdgeType($edge_name) :
          null;
      },
      $this->classes
    ));
  }
}
