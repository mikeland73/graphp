<?php

class GPRouteGenerator extends GPObject {

  private $path;
  private $controller;
  private $method;
  private $args;

  public function __construct($uri = '') {
    $this->path = explode('/', parse_url($uri)['path']);
  }

  public static function createFromParts($controller, $method = 'index', ...$args) {
    $route_generator = new GPRouteGenerator();
    $route_generator->controller = $controller;
    $route_generator->method = $method;
    $route_generator->args = $args;
    return $route_generator;
  }

  public function getPath() {
    return $this->path;
  }

  public function getController() {
    return $this->controller;
  }

  public function getMethod() {
    return $this->method;
  }

  public function getArgs() {
    return $this->args;
  }
}
