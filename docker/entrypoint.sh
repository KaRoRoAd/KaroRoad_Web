#!/bin/bash

php bin/console cache:clear

php bin/console doctrine:migrations:migrate --no-interaction

exec apache2-foreground
