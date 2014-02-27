<?

try {
  require_once '../graphp/core/GPLoader.php';
  GPRouter::init();
} catch(Exception $e) {
  // TODO (mikeland86) make this better (maybe a pretty html error or something)
  echo ' There was an exception: ';
  var_dump($e);
  // Propagate exception so that it gets logged
  throw $e;
}
