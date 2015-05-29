<?

trait GPBatch {

  public static function batchSave(array $nodes) {
    $db = GPDatabase::get();
    $db->startTransaction();
    foreach ($nodes as $node) {
      $node->save();
    }
    $db->commit();
  }

  public static function batchDelete(array $nodes) {
    $db = GPDatabase::get();
    $db->startTransaction();
    foreach ($nodes as $node) {
      $node->delete();
    }
    $db->commit();
  }

  /**
    * Deletes nodes, but ignores overriden delete() methods. More efficient but
    * won't do fancy recursive deletes.
    */
  public static function simpleBatchDelete(array $nodes) {
    GPDatabase::get()->deleteNodes($nodes);
    array_unset_keys(self::$cache, mpull($nodes, 'getID'));
  }

  public static function batchLoadConnectedNodes(
    array $nodes,
    array $edge_types,
    $force = false
  ) {
    $nodes = mpull($nodes, null, 'getID');
    $raw_edge_types = mpull($edge_types, 'getType');
    if (!$force) {
      $names = mpull($edge_types, 'getName');
      foreach ($nodes as $key => $node) {
        $valid_edge_types = array_select_keys(
          $node::getEdgeTypesByType(),
          $raw_edge_types
        );
        if ($node->isLoaded($valid_edge_types)) {
          unset($nodes[$key]);
        }
      }
    }
    $ids = GPDatabase::get()->multiGetConnectedIDs($nodes, $raw_edge_types);
    $to_nodes = self::multiGetByID(array_flatten($ids));
    foreach ($ids as $from_id => $type_ids) {
      $loaded_nodes_for_type = [];
      foreach ($type_ids as $edge_type => $ids_for_edge_type) {
        $loaded_nodes_for_type[$edge_type] = array_select_keys(
          $to_nodes,
          $ids_for_edge_type
        );
      }
      $nodes[$from_id]->connectedNodeIDs =
        array_merge_by_keys($nodes[$from_id]->connectedNodeIDs, $type_ids);
      $nodes[$from_id]->connectedNodes =
        array_merge_by_keys($nodes[$from_id]->connectedNodes, $loaded_nodes_for_type);
    }
    foreach ($nodes as $id => $node) {
      $types_for_node = $node::getEdgeTypes();
      foreach ($edge_types as $type) {
        if (
          !array_key_exists($id, $ids) &&
          !array_key_exists($type->getType(), $nodes[$id]->connectedNodes) &&
          array_key_exists($type->getName(), $types_for_node)
        ) {
          $nodes[$id]->connectedNodeIDs[$type->getType()] = [];
          $nodes[$id]->connectedNodes[$type->getType()] = [];
        }
      }
    }
  }
}
