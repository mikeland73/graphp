<?

class ArrayException extends Exception {}
class KeyNotInArrayException extends ArrayException {}

    /**
     * idxx
     *
     * @param mixed \array Description.
     * @param mixed $key        Description.
     *
     * @access public
     *
     * @return mixed Value.
     */
function idxx(array $array, $key) {
  if (!array_key_exists($key, $array)) {
    throw new KeyNotInArrayException($key . ' not in array');
  }
  return $array[$key];
}

/**
 * Returns first element in array. false if array is empty.
 * @param  array  $array
 * @return mixed        First element of array or false is array is empty
 */
function idx0(array $array) {
  return reset($array);
}
