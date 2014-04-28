<?

if (ob_get_level()) ob_end_clean();
define('ROOT_PATH', __DIR__.'/../');

try {
  require_once ROOT_PATH.'graphp/core/GPLoader.php';
  GPRouter::init();
} catch(Exception $e) {
  // TODO (mikeland86) make this better (maybe a pretty html error or something)
  echo ' There was an exception: <br /><br />';
  echo $e->getMessage() . '<br /><br />';
  echo $e . '<br /><br />';
  // Propagate exception so that it gets logged
  throw $e;
}
