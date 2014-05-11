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
    throw new KeyNotInArrayException(
      $key . ' not in array: ' . json_encode($array)
    );
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

function array_merge_by_keys() {
  $result = array();
  foreach (func_get_args() as $array) {
    foreach ($array as $key => $value) {
      $result[$key] = $value;
    }
  }
  return $result;
}

function key_by_value(array $array) {
  $result = array();
  foreach ($array as $key => $value) {
    $result[$value] = $value;
  }
  return $result;
}

function array_concat_in_place(& $arr1, $arr2) {
  foreach ($arr2 as $key => $value) {
    $arr1[] = $value;
  }
}

function array_flatten(array $array) {
  $result = array();
  foreach ($array as $key => $value) {
    if (is_array($value)){
      array_concat_in_place($result, array_flatten($value));
    } else {
      $result[$key] = $value;
    }
  }
  return $result;
}

function make_array($val) {
  return is_array($val) ? $val : [$val];
}
