#!/bin/bash

if [ "$1" = "php-fpm" ] || [ -z "$1" ]; then
    composer install
    php artisan key:generate --force
    php artisan migrate --force
    php artisan vendor:publish --provider="Dedoc\Scramble\ScrambleServiceProvider"
fi

exec "$@"
   