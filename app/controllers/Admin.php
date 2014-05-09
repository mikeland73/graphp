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
    if ($this->post->getInt('type')) {
      GPNode::createFromType($this->post->getInt('type'))->save();
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
    GP::viewWithLayout('admin/node_view', 'layout/main', ['node' => $node]);
  }
}
