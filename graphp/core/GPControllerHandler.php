<?php

/**
 * Controller handlers give you an easy way of extending controller
 * functionality and syntax without having to modify any core code. This makes
 * controller addons and extensions plug and play. Some basic extensions will be
 * included: URI, URL, Async, redirect
 *
 * Some creative uses could be:
 * -> Return if user has access to controller/method
 * -> Return API endpoint of method
 * -> Return endpoint as a manipulatable object
 * -> Return annotations for given controller method
 */
abstract class GPControllerHandler extends GPObject {

  protected $controller;

  public function __construct($controller) {
    $this->controller = $controller;
  }

  // Override to implement handler functionality
  protected abstract function handle($method, array $args);

  // Override for more complicated functionality/caching
  public static function get($controller) {
    return new static($controller);
  }

  public function __call($method, array $args) {
    $this->validateMethod($method);
    return $this->handle($method, $args);
  }

  protected function validateMethod($method) {
    if ($method) {
      if (
        !method_exists($this->controller, $method) ||
        !(new ReflectionMethod($this->controller, $method))->isPublic()
      ) {
        throw new GPException(
          $this->controller.' does not have a public method '.$method
        );
      }
    }
  }

}
