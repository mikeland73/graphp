<?php

define('ROOT_PATH', __DIR__.'/../');
date_default_timezone_set('UTC');

try {
  require_once ROOT_PATH.'graphp/core/GPLoader.php';
  GPRouter::init();
  GPRouter::route();
} catch(Exception $e) {

  $error = [];
  $error[] = 'There was an exception:';
  $error[] = $e->getMessage();
  $error[] = str_replace("\n", '<br />', $e->getTraceAsString());

  if (GPEnv::isDevEnv()) {
    echo implode('<br>', $error);
    throw $e;
  }
  error_log(implode("\n", $error));
}
