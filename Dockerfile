FROM php:8.3-cli

# Install system dependencies & PHP extensions for Laravel & QR code
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libzip-dev \
    zip \
    unzip \
    git \
    curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql gd zip bcmath \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . .

# Set permissions for Laravel storage & cache
RUN chmod -R 777 storage bootstrap/cache

# Install Composer dependencies
RUN composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

# Copy and setup entrypoint
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh \
    && sed -i -e 's/\r$//' /usr/local/bin/docker-entrypoint.sh

ENV PORT=80
EXPOSE 80

ENTRYPOINT ["docker-entrypoint.sh"]
