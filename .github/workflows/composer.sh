#!/bin/sh

COMPOSER_ROOT_VERSION=$(grep '^##' ChangeLog.md | grep -v '?' | head -1 | cut -d ' ' -f 2) composer install --prefer-dist
echo "vendor/autoload.php" > composer.pth
