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

function assert_equals($var, $val, $exception_class = 'Exception') {
  if ($var !== $val) {
    throw new $exception_class();
  }
}

function assert_in_array($idx, array $array, $exception_class = 'Exception') {
  if (!array_key_exists($idx, $array)) {
    throw new $exception_class();
  }
}
