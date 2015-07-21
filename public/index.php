<?php

define('ROOT_PATH', __DIR__.'/../');
date_default_timezone_set('UTC');

try {
  require_once ROOT_PATH.'graphp/core/GPLoader.php';
  GPRouter::init();
  GPRouter::route();
} catch(Exception $e) {

  $error = [
    'There was an exception:',
    $e->getMessage(),
    $e->getTraceAsString(),
  ];

  if (GPEnv::isDevEnv()) {
    echo str_replace("\n", '<br>', implode('<br>', $error));
    throw $e;
  }
  error_log(implode("\n", $error));
}
