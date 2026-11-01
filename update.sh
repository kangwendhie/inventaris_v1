#!/bin/bash
set -e
php artisan down --refresh=15 || true
git fetch origin main
git reset --hard origin/main
export COMPOSER_ALLOW_SUPERUSER=1
composer install --no-dev --optimize-autoloader --no-interaction
php artisan migrate --force
php artisan optimize:clear
php artisan optimize
chown -R www:www /www/wwwroot/starter.net
chmod -R 775 storage bootstrap/cache
chmod +x update.sh
php artisan up
echo "==> PEMBARUAN SELESAI!"
