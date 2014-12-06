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
}
