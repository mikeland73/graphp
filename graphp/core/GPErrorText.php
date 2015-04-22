<?

final class GPErrorText extends GPObject {

  public static function missingEdges($edges, GPNode $obj, array $loaded_ids) {
    $to_load = json_encode(mpull($edges, 'getName'));
    $possible = json_encode(
      array_values(mpull($obj::getEdgeTypes(), 'getName'))
    );
    $loaded = json_encode(array_values(mpull(
      array_select_keys($obj::getEdgeTypes(), $loaded_ids),
      'getName'
    )));
    return 'One or more of '.$to_load.' has either not been loaded or '.
           'does not exist on '.get_class($obj).'. Possible edges are '.
           $possible.' and '.'loaded edge types are '.$loaded;
  }
}
