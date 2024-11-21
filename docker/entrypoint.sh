#!/bin/bash

php bin/console cache:clear

exec docker-php-entrypoint apache2-foreground