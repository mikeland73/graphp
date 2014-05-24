<?

$start = microtime();
define('ROOT_PATH', __DIR__.'/../');

try {
  require_once ROOT_PATH.'graphp/core/GPLoader.php';
  GPRouter::init();
} catch(Exception $e) {
  // TODO (mikeland86) make this better (maybe a pretty html error or something)
  echo ' There was an exception: <br />';
  echo $e->getMessage() . '<br />';
  echo str_replace("\n", '<br />', $e->getTraceAsString())  . '<br /><br />';
  // Propagate exception so that it gets logged
  throw $e;
}

var_dump(microtime() - $start);
