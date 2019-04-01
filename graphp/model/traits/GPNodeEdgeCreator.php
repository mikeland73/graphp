<?php

trait GPNodeEdgeCreator {

  public static function edge(string $name = null) {
    return new GPEdgeType(get_called_class(), $name ?: STR::pluralize(get_called_class()));
  }

  public static function singleEdge(string $single_name = null, string $plural_name = null) {
    $defaulted_single_name = $single_name ?: get_called_class();
    $defaulted_plural_name = $plural_name ?: STR::pluralize($defaulted_single_name);
    return (new GPEdgeType(get_called_class(), $defaulted_plural_name))
      ->setSingleNodeName($defaulted_single_name);
  }
}
