#!/bin/sh
set -e

PORT="${PORT:-80}"
echo "=========================================="
echo "Starting SMP IT Al-Muttaqin Laravel Server"
echo "Listening on Host: 0.0.0.0 Port: $PORT"
echo "=========================================="

php artisan config:clear || true
php artisan view:clear || true
php artisan route:clear || true

echo "Running migrations..."
php artisan migrate --force || true

echo "Running seeders..."
php artisan db:seed --force || true

echo "Serving Laravel on 0.0.0.0:$PORT..."
exec php artisan serve --host=0.0.0.0 --port="$PORT"
