#!/bin/bash

if [ ! -d "vendor" ]; then
    composer install
fi

php bin/console cache:clear

php bin/console doctrine:migrations:migrate --no-interaction

php bin/console messenger:consume async -vv

php bin/console lexik:jwt:generate-keypair

exec apache2-foreground
