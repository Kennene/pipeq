#!/bin/sh
set -e

if [ ! -f "database/database.sqlite" ]; then
    touch database/database.sqlite
    chown -R www-data:www-data database
    php artisan --no-interaction --force migrate:fresh --seed
fi

php artisan --no-interaction optimize:clear && php artisan --no-interaction optimize

exec "$@"