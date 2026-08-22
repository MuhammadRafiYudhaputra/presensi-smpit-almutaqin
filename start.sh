#!/bin/sh

echo "=========================================="
echo "Starting SMP IT Al-Muttaqin Laravel Server"
echo "Target Port: ${PORT:-80}"
echo "=========================================="

php artisan config:clear
php artisan view:clear
php artisan route:clear

echo "Running migrations..."
php artisan migrate --force || true

echo "Running seeders..."
php artisan db:seed --force || true

echo "Starting php artisan serve on port ${PORT:-80}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT:-80}"
