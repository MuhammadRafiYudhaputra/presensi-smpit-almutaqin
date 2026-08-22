#!/bin/bash
set -e

PORT="${PORT:-80}"
echo "Booting Laravel Application on Port: $PORT ..."

# Clear all cached configs
php artisan config:clear || true
php artisan view:clear || true
php artisan route:clear || true

# Run database migrations and seeders automatically
echo "Migrating database..."
php artisan migrate --force || true
echo "Seeding database..."
php artisan db:seed --force || true

# Dynamically set Apache port
sed -i "s/Listen .*/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf
sed -i "s/<VirtualHost \*:[0-9]*>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/*.conf

echo "Starting Apache on port $PORT..."
exec apache2-foreground
