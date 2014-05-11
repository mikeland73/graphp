<?

class Wallet extends GPNode {

    protected static $data_types = [
      'size' => [GPDataTypes::GP_STRING, GPDataTypes::INDEXED],
    ];

    protected static function getEdgeTypesImpl() {
      return [
        new GPEdge(Wallet::getType(), User::getType(), 'Owner'),
      ];
  }

}
