<?php

final class GPErrorText extends GPObject {

  public static function missingEdges($edges, GPNode $obj, array $loaded_ids) {
    $to_load = mpull($edges, 'getName');
    $possible = array_values(mpull($obj::getEdgeTypes(), 'getName'));
    $loaded = array_values(mpull(
      array_select_keys($obj::getEdgeTypes(), $loaded_ids),
      'getName'
    ));
    if (array_intersect($to_load, $possible) === $to_load) {
      return 'Unloaded edges: '.json_encode(array_diff($to_load, $loaded)).
        ' You must load connected nodes before you can use them.';
    }

    return 'One or more of '.json_encode($to_load).' has either not been loaded or '.
           'does not exist on '.get_class($obj).'. Possible edges are '.
           json_encode($possible).' and '.'loaded edge types are '.json_encode($loaded);
  }

  public static function wrongArgs($class, $method, $expected, $actual) {
    return
      $class.'::'.$method.' expected '.$expected.' arguments, got '.$actual;
  }

  public static function missingEdgeType($class, $edge_name, $possible_edges) {
    return $class.' does not have an edge "'.$edge_name.'". Possible edges'.
      ' are: '.json_encode(array_keys($possible_edges));
  }
}
