#!/bin/sh
set -e

mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link || true

exec "$@"
