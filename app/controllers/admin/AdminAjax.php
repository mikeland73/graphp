<?php

class AdminAjax extends GPController {

  public function __construct() {
    if (!GPConfig::get()->admin_enabled) {
      GP::return404();
    }
  }

  public function create($type) {
    if ($this->post->getExists('create')) {
      GPNode::createFromType($type)->save();
    }
    Admin::redirect('node_type', $type);
  }

  public function delete($type) {
    if ($this->post->getInt('delete_node_id')) {
      GPNode::getByID($this->post->getInt('delete_node_id'))->delete();
    }
    Admin::redirect('node_type', $type);
  }

  public function save($id) {
    $node = GPNode::getByID($id);
    $key = $this->post->getString('data_key');
    $val = $this->post->getString('data_val');
    $key_to_unset = $this->post->getString('data_key_to_unset');
    if ($key && $val) {
      $data_type = $node::getDataTypeByName($key);
      if (
        $data_type !== null &&
        $data_type->getType() === GPDataType::GP_ARRAY
      ) {
        $val = json_decode($val, true);
      } else if (
        $data_type !== null &&
        $data_type->getType() === GPDataType::GP_BOOL
      ) {
        $val = (bool)$val;
      }
      $node->setData($key, $val)->save();
    }
    if ($key_to_unset) {
      $node->unsetData($key_to_unset)->save();
    }
    $edge_type = $this->post->get('edge_type');
    if ($edge_type && $this->post->getInt('to_id')) {
      $edge = is_numeric($edge_type) ?
        $node::getEdgeTypeByType($edge_type) :
        $node::getEdgeType($edge_type);
      $other_node = GPNode::getByID($this->post->getInt('to_id'));
      if ($this->post->getExists('delete')) {
        $node->addPendingRemovalNodes($edge, [$other_node]);
      } else {
        $node->addPendingConnectedNodes($edge, [$other_node]);
      }
      $node->save();
    }
    Admin::redirect('node', $id);
  }
}
