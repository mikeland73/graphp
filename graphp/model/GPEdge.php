<?

class GPEdge extends GPNode {

  const TYPE = 1000;

  protected static $data_types = [
    'fromType' => GPDataTypes::GP_NODE_ID,
    'toType' => GPDataTypes::GP_NODE_ID,
    'name' => GPDataTypes::GP_STRING,
  ];

  public function getEdgeType() {
     return STRUtils::to64BitInt($this->getName());
  }

}
