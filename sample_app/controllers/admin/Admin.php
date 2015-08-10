<?php

class Admin extends GPController {

  public function __construct() {
    if (!GPConfig::get()->admin_enabled) {
      GP::return404();
    }
  }

  public function index() {
    $data = [
      'types' => GPNodeMap::regenAndGetAllTypes(),
      'counts' => ipull(GPDatabase::get()->getTypeCounts(), 'count', 'type'),
    ];
    GPDatabase::get()->dispose();
    GP::viewWithLayout('admin/explore_view', 'layout/admin_layout', $data);
  }

  public function node_type($type) {
    $name = GPNodeMap::getClass($type);
    $data = [
      'type' => $type,
      'name' => $name,
      'nodes' => $name::getAll(),
    ];
    GPDatabase::get()->dispose();
    GP::viewWithLayout('admin/node_type_view', 'layout/admin_layout', $data);
  }

  public function node($id) {
    $node = GPNode::getByID($id);
    if (!$node) {
      self::redirect();
    }
    $node->loadConnectedNodes($node::getEdgeTypes());
    GP::viewWithLayout(
      'admin/node_view',
      'layout/admin_layout',
      ['node' => $node]
    );
  }

  public function edges() {
    $node_classes = GPNodeMap::regenAndGetAllTypes();
    $edges = [];
    foreach ($node_classes as $class) {
      array_concat_in_place($edges, $class::getEdgeTypes());
    }
    GP::viewWithLayout(
      'admin/edge_view',
      'layout/admin_layout',
      ['edges' => $edges]
    );
  }
}
