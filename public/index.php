<?

define('ROOT_PATH', __DIR__.'/../');
date_default_timezone_set('UTC');

try {
  require_once ROOT_PATH.'graphp/core/GPLoader.php';
  GPRouter::init();
  GPRouter::route();
} catch(Exception $e) {
  // TODO (mikeland86) make this better (maybe a pretty html error or something)
  echo ' There was an exception: <br />';
  echo $e->getMessage() . '<br />';
  echo str_replace("\n", '<br />', $e->getTraceAsString())  . '<br /><br />';
  // Propagate exception so that it gets logged
  throw $e;
}
