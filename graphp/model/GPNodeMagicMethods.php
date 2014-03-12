<?

trait GPNodeMagicMethods {

  // Proposed magic API:
  //
  // getX() should only work if x is defined in data_types
  //
  // setY() should only work if y is defined in data_types
  //
  // unsetZ() should only work if z is defined in data_types
  //
  // getConnectedFoo should only work if Foo is the name of a loaded edge
  // (returns one node)
  // getAllConnectedFoo same as getConnectedFoo but returns all (array)
  //
  // addConnectedBar Should only work if Bar is name of edge. Adds a Bar
  // type edge.
  //
  // removeConnected[edge_type]($node) should remove edge to that node
  // removeAllConnected[edge_type] should remove all edges of that type
  //
  // loadConnected[edge_type] called to load edges (and end nodes) of that type
  // loadConnected[edge_type]IDs called to load edges (and end ids) of that type
  //
  // save() must be called for all set() and remove operations to be performed
  //

  public function __call($method, $args) {
    if (substr_compare($method, 'getConnected', 0, 12) === 0) {
      //TODO
    } else if (substr_compare($method, 'getAllConnected', 0, 15) === 0) {
    } else if (substr_compare($method, 'addConnected', 0, 12) === 0) {
    } else if (substr_compare($method, 'removeConnected', 0, 15) === 0) {
    } else if (substr_compare($method, 'removeAllConnected', 0, 18) === 0) {
    } else if (substr_compare($method, 'loadConnected', 0, 13) === 0) {
    } else if (substr_compare($method, 'loadConnectedIDs', 0, 16) === 0) {
    } else if (substr_compare($method, 'get', 0, 3) === 0) {
      return $this->getDataX(mb_substr($method, 3));
    } else if (substr_compare($method, 'set', 0, 3) === 0) {
      assert_equals(count($args), 1, 'GPBadArgException');
      return $this->setDataX(mb_substr($method, 3), idx0($args));
    } else if (substr_compare($method, 'unset', 0, 3) === 0) {
      return $this->unsetDataX(mb_substr($method, 3));
    } else {
      throw new GPBadMethodCallException();
    }
    return $this;
  }

}
