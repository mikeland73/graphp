<?php

class GPRouter extends GPObject {

  private static $routes;
  private static $parts;
  private static $controller;
  private static $method;

  public static function init() {
    self::$routes = require_once ROOT_PATH.'config/routes.php';
    self::$parts = self::getParts();
  }

  public static function route() {
    $controller_name = ucfirst(idxx(self::$parts, 0));
    if (!class_exists($controller_name)) {
      GP::return404();
    }
    self::$method = idx(self::$parts, 1, 'index');
    self::$controller = new $controller_name();
    if (!method_exists(self::$controller, self::$method)) {
      GP::return404();
    }
    self::$controller->init();
    $args = array_slice(self::$parts, 2);
    call_user_func_array([self::$controller, self::$method], $args);
  }

  public static function getController() {
    return self::$controller;
  }

  public static function getMethod() {
    return self::$method;
  }

  private static function getParts() {
    if (GP::isCLI()) {
      global $argv;
      $uri = idx($argv, 1, '');
    } else {
      $uri = $_SERVER['REQUEST_URI'];
    }
    $uri = str_replace('index.php', '', $uri);
    $uri = preg_replace(['/\?.*/', '#[/]+$#'], '', $uri);
    if (!$uri && isset(self::$routes['__default__'])) {
      return self::$routes['__default__'];
    }
    foreach (array_keys(self::$routes) as $regex) {
      $matches = [];
      if (preg_match('#'.$regex.'#', $uri, $matches)) {
        $parts = self::$routes[$regex];
        array_concat_in_place($parts, array_slice($matches, 1));
        return $parts;
      }
    }
    return array_values(array_filter(
      explode('/', $uri),
      function($part) {
        return mb_strlen($part) > 0; // Stupid PHP filters out '0'
      }
    ));
  }
}
