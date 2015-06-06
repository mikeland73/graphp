<?php

final class Comment extends GPNode {

  protected static function getDataTypesImpl() {
    return [
      new GPDataType('text', GPDataType::GP_STRING),
    ];
  }

  protected static function getEdgeTypesImpl() {
    return [
      (new GPEdgeType(Post::class))->setSingleNodeName('post'),
      (new GPEdgeType(User::class, 'creators'))->setSingleNodeName('creator'),
    ];
  }
}
