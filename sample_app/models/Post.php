<?php

final class Post extends GPNode {

  protected static function getDataTypesImpl() {
    return [
      new GPDataType('text', GPDataType::GP_STRING),
    ];
  }

  protected static function getEdgeTypesImpl() {
    return [
      (new GPEdgeType(Comment::class, 'comments'))
        ->inverse(Comment::getEdgeType('post')),
      (new GPEdgeType(User::class, 'creators'))->setSingleNodeName('creator'),
    ];
  }
}
