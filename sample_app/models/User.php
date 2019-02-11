<?php

final class User extends GPNode {

  protected static function getDataTypesImpl() {
    return [
      new GPDataType('email', GPDataType::GP_STRING, true),
      new GPDataType('password', GPDataType::GP_STRING),
    ];
  }

  protected static function getEdgeTypesImpl() {
    return [
      (new GPEdgeType(Post::class, 'posts'))
        ->inverse(Comment::getEdgeType('creator')),
      (new GPEdgeType(Comment::class, 'comments'))
        ->inverse(Comment::getEdgeType('creator')),
    ];
  }
}
