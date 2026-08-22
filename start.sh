#!/bin/sh

PORT="${PORT:-80}"

echo "=========================================="
echo "Starting SMP IT Al-Muttaqin Laravel"
echo "Port: $PORT"
echo "=========================================="

php artisan config:clear || true
php artisan view:clear || true
php artisan route:clear || true

echo "Migrating database..."
php artisan migrate --force || true

echo "Seeding database..."
php artisan db:seed --force || true

echo "PHP server running on 0.0.0.0:$PORT..."
exec php -S 0.0.0.0:$PORT -t public
