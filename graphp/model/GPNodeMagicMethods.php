<?php

trait GPNodeMagicMethods {

  private static $methodCache = [];

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

    $cache_key = get_class($this).$method;
    if (array_key_exists($cache_key, self::$methodCache)) {
      return call_user_func(self::$methodCache[$cache_key], $this);
    }

    if (substr_compare($method, 'get', 0, 3) === 0) {
      return $this->handleGet(mb_substr($method, 3), $args);
    }

    if (substr_compare($method, 'is', 0, 2) === 0) {
      $name = mb_substr($method, 2);
      $type = static::getDataTypeByName($name);
      if ($type && $type->getType() === GPDataType::GP_BOOL) {
        return $this->handleGet(mb_substr($method, 2), $args);
      }
    }

    if (substr_compare($method, 'set', 0, 3) === 0) {
      Assert::equals(
        count($args),
        1,
        GPErrorText::wrongArgs(get_called_class(), $method, 1, count($args))
      );
      return $this->setDataX(strtolower(mb_substr($method, 3)), idx0($args));
    }

    if (substr_compare($method, 'add', 0, 3) === 0) {
      $edge = static::getEdgeType(mb_substr($method, 3));
      return $this->addPendingConnectedNodes($edge, make_array(idx0($args)));
    }

    if (substr_compare($method, 'removeAll', 0, 9) === 0) {
      $edge = static::getEdgeType(mb_substr($method, 9));
      return $this->addPendingRemovalAllNodes($edge);
    }

    if (substr_compare($method, 'remove', 0, 6) === 0) {
      $edge = static::getEdgeType(mb_substr($method, 6));
      return $this->addPendingRemovalNodes($edge, make_array(idx0($args)));
    }

    if (substr_compare($method, 'forceLoad', 0, 9) === 0) {
      return $this->handleLoad(mb_substr($method, 5), $args, true);
    }

    if (substr_compare($method, 'load', 0, 4) === 0) {
      return $this->handleLoad($method, $args);
    }

    if (substr_compare($method, 'unset', 0, 3) === 0) {
      return $this->unsetDataX(strtolower(mb_substr($method, 5)));
    }

    throw new GPException(
      'Method '.$method.' not found in '.get_called_class()
    );
  }

  private function handleGet($name, $args) {
    $lower_name = strtolower($name);
    // Default to checking data first.
    $type = static::getDataTypeByName($lower_name);
    if ($type) {
      self::$methodCache[get_class($this).'get'.$name] =
        function($that) use ($type, $lower_name) {
          return $that->getData($lower_name, $type->getDefault());
        };
      return $this->getData($lower_name, $type->getDefault());
    }

    if (substr_compare($name, 'One', 0, 3, true) === 0) {
      $name = str_ireplace('One', '', $name);
      $one = true;
    }

    if (substr_compare($name, 'IDs', -3) === 0) {
      $name = str_ireplace('IDs', '', $name);
      $ids_only = true;
    } else if (substr_compare($name, 'ID', -2) === 0) {
      $name = str_ireplace('ID', '', $name);
      $ids_only = true;
    }

    $name = strtolower($name);

    $edge = static::getEdgeType($name);
    if ($edge) {
      if ($name === $edge->getSingleNodeName()) {
        $one = true;
      }
      if (empty($ids_only)) {
        $result = $this->getConnectedNodes([$edge]);
      } else {
        $result = $this->getConnectedIDs([$edge]);
      }
      $nodes = idx($result, $edge->getType(), []);
      return empty($one) ? $nodes : idx0($nodes);
    } else {
      throw new GPException(
        'Getter did not match any data or edge',
        1
      );
    }
  }

  private function handleLoad($method, $args, $force = false) {
    $limit = null;
    $offset = 0;
    if (count($args) === 1) {
      $limit = idx0($args);
    } else if (count($args) === 2) {
      list($limit, $offset) = $args;
    }
    if (substr_compare($method, 'IDs', -3) === 0) {
      $edge_name = mb_substr($method, 4, -3);
      $edge = static::getEdgeType($edge_name);
      return $this->loadConnectedIDs([$edge], $force, $limit, $offset);
    } else {
      $edge_name = mb_substr($method, 4);
      $edge = static::getEdgeType($edge_name);
      return $this->loadConnectedNodes([$edge], $force, $limit, $offset);
    }
  }
}
