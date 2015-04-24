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
    array $edge_types
  ) {
    $nodes = mpull($nodes, null, 'getID');
    $raw_edge_types = mpull($edge_types, 'getType');
    $ids = GPDatabase::get()->multiGetConnectedIDs($nodes, $raw_edge_types);
    $to_nodes = self::multiGetByID(array_flatten($ids));
    foreach ($ids as $from_id => $type_ids) {
      foreach ($type_ids as $edge_type => & $ids_for_edge_type) {
        foreach ($ids_for_edge_type as $key => $id) {
          $ids_for_edge_type[$key] = $to_nodes[$id];
        }
      }
      $nodes[$from_id]->connectedNodes =
        array_merge_by_keys($nodes[$from_id]->connectedNodes, $type_ids);
    }
    foreach ($nodes as $id => $node) {
      $types_for_node = $node::getEdgeTypes();
      foreach ($edge_types as $type) {
        if (
          !array_key_exists($id, $ids) &&
          !array_key_exists($type->getType(), $nodes[$id]->connectedNodes) &&
          array_key_exists($type->getName(), $types_for_node)
        ) {
          $nodes[$id]->connectedNodes[$type->getType()] = [];
        }
      }
    }
  }
}
