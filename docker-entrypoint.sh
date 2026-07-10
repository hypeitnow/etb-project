#!/bin/sh
set -e

php artisan migrate --force

if [ "$(php artisan tinker --execute='echo \App\Models\User::count();' 2>/dev/null)" = "0" ]; then
    php artisan db:seed --class=DatabaseSeeder --force
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache

exec php artisan serve --host=0.0.0.0 --port=8080
