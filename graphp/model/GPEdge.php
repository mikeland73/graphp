<?

class GPEdge extends GPNode {

  const TYPE = 1000;

  protected static $data_types = [
    'from_type' => GPDataTypes::GP_NODE_ID,
    'to_type' => GPDataTypes::GP_NODE_ID,
    'name' => GPDataTypes::GP_STRING,
  ];

}
