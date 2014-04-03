<?

function assert_true($var, $exception_class = 'Exception') {
  if (!$var) {
    throw new $exception_class();
  }
}

function assert_false($var, $exception_class = 'Exception') {
  if ($var) {
    throw new $exception_class();
  }
}

function assert_equals($var, $val, $message = '') {
  if ($var !== $val) {
    throw new Exception(
      'Failed asserting that '.$var.' is equal to '.$val.' - '.$message
    );
  }
}

function assert_in_array($idx, array $array, $exception_class = 'Exception') {
  if (!array_key_exists($idx, $array)) {
    throw new $exception_class($idx.' not in '.json_encode($array));
  }
}
