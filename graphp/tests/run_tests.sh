#!/bin/sh

DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
cd $DIR
../../third_party/vendor/bin/phpunit --bootstrap bootstrap.php .
