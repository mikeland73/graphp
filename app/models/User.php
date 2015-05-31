<?php

final class User extends GPNode {

  protected static function getDataTypesImpl() {
    return [
      new GPDataType('name', GPDataType::GP_STRING, true),
    ];
  }

  protected static function getEdgeTypesImpl() {
    return [
      new GPEdgeType(BankAccount::class),
    ];
  }

}
