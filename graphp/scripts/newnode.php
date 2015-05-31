#!/usr/bin/env php
<?php

// input argument should be of the form "nodeName[args]" where args are 0
// or more comma separated strings of arguments that will be indexed.

if (!isset($argv[1])) {
  throw new Exception('Bad arguments');
}
$tokens = explode('[', $argv[1]);

echo 'Creating node ' . $tokens[0];

if (isset($tokens[1])) {
  $indexes = explode(',', mb_substr($tokens[1], 0, mb_strlen($tokens[1]) - 1));
  echo ' with indexes: ';
  echo implode(', ', $indexes);
}


// Create new Node file (clone template)
// Add indexed field to indexed_data_type array
// Add new type to node map

echo PHP_EOL;
