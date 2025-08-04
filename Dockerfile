FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip \
    unzip \
    cron \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd

# Install Xdebug
RUN pecl install xdebug \
    && docker-php-ext-enable xdebug

# Xdebug configuration (default: remote debug enabled)
COPY ./config/php/xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . .

# Install PHP dependencies
RUN composer install --no-dev --optimize-autoloader

# Set permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www/storage \
    && chmod -R 755 /var/www/logs

# Add cron job for monitoring
RUN echo "* * * * * cd /var/www && /usr/local/bin/php monitor.php >> /var/www/logs/monitor.log 2>&1" | crontab -

# Start cron and PHP-FPM
CMD ["sh", "-c", "cron && php-fpm"]

EXPOSE 9000



