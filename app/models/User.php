<?

class User extends GPNode {

  protected static $data_types = [
    'name' => [GPDataTypes::GP_STRING, GPDataTypes::INDEXED],
  ];

  const TYPE = 1002;

}
