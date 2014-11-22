<?

final class Wallet extends GPNode {

    protected static function getDataTypesImpl() {
      return [
        new GPDataType('size', GPDataType::GP_STRING, true),
      ];
    }

    protected static function getEdgeTypesImpl() {
      return [
        new GPEdgeType(User::class, 'Owner'),
      ];
  }

}
