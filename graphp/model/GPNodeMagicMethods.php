<?

trait GPNodeMagicMethods {

  // Magic API:
  //
  // getX() should only work if the node has a GPDataType with the name x
  // returned by getDataTypeImpl() or if the node has a GPEdgeType with that
  // name that has previously loaded. Calling getX on an unloaded edge will
  // throw.
  //
  // setY() should only work if y is defined in data_types. (Not for edges)
  //
  // unsetZ() should only work if z is defined in data_types.
  //
  // addFoo() should only work if Foo is a defined edge for that node.
  //
  // getOneFoo Is like getFoo for edges but returns only the first result.
  //
  // remove[edge_type]($node) should remove edge to that node
  // removeAll[edge_type] should remove all edges of that type
  //
  // load[edge_type] called to load edges (and end nodes) of that type
  // load[edge_type]IDs called to load edges (and end ids) of that type
  //
  // save() must be called for all set, add and remove operations to be
  // performed
  //

  public function __call($method, $args) {

    if (substr_compare($method, 'get', 0, 3) === 0) {
      return $this->handleGet($method, $args);
    }

    if (substr_compare($method, 'set', 0, 3) === 0) {
      Assert::equals(count($args), 1, 'GPException');
      return $this->setDataX(mb_strtolower(mb_substr($method, 3)), idx0($args));
    }

    if (substr_compare($method, 'add', 0, 3) === 0) {
      $edge = static::getEdgeType(mb_substr($method, 3));
      return $this->addPendingConnectedNodes($edge, $args);
    }

    if (substr_compare($method, 'remove', 0, 6) === 0) {
      $edge = static::getEdgeType(mb_substr($method, 6));
      return $node->addPendingRemovalNodes($edge, idx0($args));
    }

    if (substr_compare($method, 'removeAll', 0, 9) === 0) {
      $edge = static::getEdgeType(mb_substr($method, 9));
      return $node->addPendingRemovalAllNodes($edge);
    }

    if (substr_compare($method, 'load', 0, 4) === 0) {
      return $this->handleLoad($method, $args);
    }

    if (substr_compare($method, 'unset', 0, 3) === 0) {
      return $this->unsetDataX(mb_strtolower(mb_substr($method, 5)));
    }

    throw new GPException(
      'Method '.$method.' not found in '.get_called_class()
    );
  }

  private function handleGet($method, $args) {
    if (substr_compare($method, 'one', 3, 3, true) === 0) {
      $method = str_ireplace('one', '', $method);
      $one = true;
    }
    $name = mb_strtolower(mb_substr($method, 3));

    if (static::getDataTypeByName($name)) {
      return $this->getDataX($name);
    } else if (($edge = static::getEdgeType($name))) {
      $result = $this->getConnectedNodes(array($edge));
      $nodes = idx($result, $edge->getType(), array());
      return empty($one) ? $nodes : idx0($nodes);
    } else {
      throw new GPException(
        'Getter did not match any data or edge',
        1
      );
    }
  }

  private function handleLoad($method, $args) {
    if (substr_compare($method, 'IDs', -3) === 0) {
      $edge_name = mb_substr($method, 4, strlen($method) - 16);
      $edge = static::getEdgeType($edge_name);
      return $this->loadConnectedIDs(array($edge));
    } else {
      $edge_name = mb_substr($method, 4);
      $edge = static::getEdgeType($edge_name);
      return $this->loadConnectedNodes(array($edge));
    }
  }

}
