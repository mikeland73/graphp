#!/bin/sh

DIR=$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )
cd $DIR
../../third_party/PHPUnit/phpunit.phar --bootstrap bootstrap.php .
