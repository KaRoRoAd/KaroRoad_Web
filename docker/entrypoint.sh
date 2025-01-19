#!/bin/bash

php bin/console cache:clear

exec apache2-foreground
