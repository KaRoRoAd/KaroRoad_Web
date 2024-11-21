#!/bin/bash

if [ ! -d "vendor" ]; then
  composer install
fi

php bin/console cache:clear

exec docker-php-entrypoint apache2-foreground