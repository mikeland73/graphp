<?

class User extends GPNode {

  protected static $data_types = [
    'name' => [GPDataTypes::GP_STRING, GPDataTypes::INDEXED],
  ];

  protected static function getEdgeTypesImpl() {
    return [
      new GPEdge(static::getType(), BankAccount::getType(), 'BankAccount'),
    ];
  }

}
