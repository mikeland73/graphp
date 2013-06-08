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
