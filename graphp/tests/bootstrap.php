<?php

// This is used to bootstrap the framework without routing.
// It is useful for tests and other require() cases.
// For cli, you can use public/index.php and provide a controller and method
// as arguments.

define('ROOT_PATH', __DIR__.'/../../');
date_default_timezone_set('UTC');

try {
  require_once ROOT_PATH.'graphp/core/GPLoader.php';
  GPRouter::init();
} catch(Exception $e) {
  echo ' There was an exception: <br />';
  echo $e->getMessage() . '<br />';
  echo str_replace("\n", '<br />', $e->getTraceAsString())  . '<br /><br />';
  // Propagate exception so that it gets logged
  throw $e;
}
