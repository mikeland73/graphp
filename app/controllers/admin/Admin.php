<?

class Admin extends GPController {

  public function index() {
    $data = [
      'types' => GPNodeMap::regenAndGetAllTypes(),
      'counts' => ipull(GPDatabase::get()->getTypeCounts(), 'count', 'type'),
    ];
    GP::viewWithLayout('admin/explore_view', 'layout/main', $data);
  }

  public function node_type($type) {
    if ($this->post->getExists('create')) {
      GPNode::createFromType($type)->save();
    }
    if ($this->post->getInt('delete_node_id')) {
      GPNode::getByID($this->post->getInt('delete_node_id'))->delete();
    }
    $name = GPNodeMap::getClass($type);
    $data = [
      'type' => $type,
      'name' => $name,
      'nodes' => $name::getAll(),
    ];
    GP::viewWithLayout('admin/node_type_view', 'layout/main', $data);
  }

  public function node($id) {
    $node = GPNode::getByID($id);
    $key = $this->post->getString('data_key');
    $val = $this->post->getString('data_val');
    $key_to_unset = $this->post->getString('data_key_to_unset');
    if ($key && $val) {
      $node->setData($key, $val)->save();
    }
    if ($key_to_unset) {
      $node->unsetData($key_to_unset)->save();
    }
    if ($this->post->getInt('edge_type') && $this->post->getInt('to_id')) {
      $edge = $node::getEdgeTypeByType($this->post->getInt('edge_type'));
      $other_node = GPNode::getByID($this->post->getInt('to_id'));
      if ($this->post->getExists('delete')) {
        $node->addPendingRemovalNodes($edge, [$other_node]);
      } else {
        $node->addPendingConnectedNodes($edge, [$other_node]);
      }
      $node->save();
    }
    $node->loadConnectedNodes($node::getEdgeTypes());
    GP::viewWithLayout('admin/node_view', 'layout/main', ['node' => $node]);
  }

  public function edges() {
    $node_classes = GPNodeMap::regenAndGetAllTypes();
    $edges = [];
    foreach ($node_classes as $class) {
      array_concat_in_place($edges, $class::getEdgeTypes());
    }
    GP::viewWithLayout('admin/edge_view', 'layout/main', ['edges' => $edges]);
  }
}
