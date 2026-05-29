#!/usr/bin/env bash
set -e

cd /var/www/html

mkdir -p storage/framework/{cache,sessions,views} storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R ug+rwx storage bootstrap/cache

if [ ! -f .env ]; then
  cp .env.example .env
fi

php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true
php artisan cache:clear || true
php artisan package:discover --ansi || true

exec "$@"
