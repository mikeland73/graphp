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
    if (is_array(self::$parts)) {
      $generator = GPRouteGenerator::createFromParts(...self::$parts);
    } else if (idx(class_parents(self::$parts), GPRouteGenerator::class)) {
      $generator = new self::$parts($_SERVER['REQUEST_URI']);
    } else {
      throw new GPException('Unrecognized route value');
    }

    $controller_name = $generator->getController();

    if (!class_exists($controller_name)) {
      GP::return404();
    }

    self::$controller = new $controller_name();
    self::$method = $generator->getMethod();

    if (!method_exists(self::$controller, self::$method)) {
      GP::return404();
    }

    if (!self::$controller->isAllowed(self::$method)) {
      GP::return404();
    }

    self::$controller->init();
    call_user_func_array([self::$controller, self::$method], $generator->getArgs());
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
        if (is_array($parts)) {
          array_concat_in_place($parts, array_slice($matches, 1));
        }
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
