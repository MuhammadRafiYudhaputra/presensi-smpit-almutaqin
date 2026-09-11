#!/bin/sh

PORT="${PORT:-80}"

echo "=========================================="
echo "Starting SMP IT Al-Muttaqin Laravel"
echo "Port: $PORT"
echo "=========================================="

mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache/data storage/logs bootstrap/cache
chmod -R 777 storage bootstrap/cache

php artisan config:clear || true
php artisan view:clear || true
php artisan route:clear || true

echo "Running migrations..."
php artisan migrate --force || true

echo "Running seeders..."
php artisan db:seed --force || true

echo "Starting queue worker..."
php artisan queue:work --sleep=3 --tries=3 --timeout=90 > storage/logs/queue.log 2>&1 &

echo "PHP server running on 0.0.0.0:$PORT..."
exec php -S 0.0.0.0:$PORT -t public
