#!/bin/sh
set -e

# Check if the database file exists. If it doesn't, create it and run the migrations and seeders
if [ ! -f "database/database.sqlite" ]; then
    touch database/database.sqlite
    chown -R www-data:www-data database
    php artisan --no-interaction --force migrate:fresh --seed

    # Since it is first run of this application, change the Laravel APP_KEY
    php artisan --no-interaction --force --env=production key:generate
fi

# Clear the cache and optimize the application
php artisan --no-interaction optimize:clear && php artisan --no-interaction optimize

exec "$@"