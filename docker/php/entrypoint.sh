#!/bin/sh
set -e

rsync -a --delete \
    --exclude storage \
    --exclude .env \
    /var/www/html-src/ /var/www/html/

mkdir -p /var/www/html/storage
rsync -a --ignore-existing /var/www/html-src/storage/ /var/www/html/storage/

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html-src/.env.example /var/www/html/.env 2>/dev/null || true
fi

exec "$@"
