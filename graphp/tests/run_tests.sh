#!/bin/sh

DIR=$(dirname "$0")
cd $DIR
../../third_party/vendor/bin/phpunit --bootstrap bootstrap.php .
