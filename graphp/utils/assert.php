<?

function assert_true($var, $message = '') {
  if (!$var) {
    throw new GPException(
      'Failed asserting that '.$var.' is true - '.$message
    );
  }
}

function assert_false($var, $message = '') {
  if ($var) {
    throw new GPException(
      'Failed asserting that '.$var.' is false - '.$message
    );
  }
}

function assert_equals($var, $val, $message = '') {
  if ($var !== $val) {
    throw new GPException(
      'Failed asserting that '.$var.' is equal to '.$val.' - '.$message
    );
  }
}

function assert_in_array($idx, array $array, $message = '') {
  if (!array_key_exists($idx, $array)) {
    throw new GPException($idx.' not in '.json_encode($array));
  }
}
