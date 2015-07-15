<?php

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
function idxx(array $array, $key, $message = '') {
  if (!array_key_exists($key, $array)) {
    throw new GPException(
      $message ?: $key . ' not in array: ' . json_encode($array)
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

function array_concat(array $arr1 /*array2, ...*/) {
  $args = func_get_args();
  foreach ($args as $key => $arr2) {
    if ($key !== 0) {
      foreach ($arr2 as $key => $value) {
        $arr1[] = $value;
      }
    }
  }
  return $arr1;
}

function array_flatten(array $array) {
  $result = array();
  foreach ($array as $key => $value) {
    if (is_array($value)){
      array_concat_in_place($result, array_flatten($value));
    } else {
      $result[] = $value;
    }
  }
  return $result;
}

function make_array($val) {
  return is_array($val) ? $val : [$val];
}

function array_select_keysx(array $dict, array $keys, $custom_error = null) {
  $result = array();
  foreach ($keys as $key) {
    if (array_key_exists($key, $dict)) {
      $result[$key] = $dict[$key];
    } else {
      if (is_callable($custom_error)) {
        $custom_error();
      } else {
        throw new GPException('Missing key '.$key.' in '.json_encode($dict), 1);
      }
    }
  }
  return $result;
}

function array_unset_keys(array & $array, array $keys) {
  foreach ($keys as $key) {
    unset($array[$key]);
  }
}

function array_append(array $array, $elem, $key = null) {
  if ($key === null) {
    $array[] = $elem;
  } else {
    $array[$key] = $elem;
  }
  return $array;
}
