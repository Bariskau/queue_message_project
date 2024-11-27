FROM php:8.1-fpm

ARG UID=1000
ARG GID=1000

# System dependencies
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libpq-dev \
    libzip-dev \
    zip \
    unzip \
    supervisor \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd pdo pdo_pgsql pgsql zip pcntl

# Install Redis extension
RUN pecl install redis && docker-php-ext-enable redis

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
RUN composer self-update --2

# Configure PHP
COPY docker/php/php.ini /usr/local/etc/php/conf.d/custom.ini

# Configure Supervisor
COPY docker/config/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Update www-data user with host user UID/GID
RUN usermod -u ${UID} www-data && groupmod -g ${GID} www-data

WORKDIR /var/www/html

# Önce composer dosyalarını kopyala ve bağımlılıkları yükle
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --prefer-dist

# Tüm uygulama dosyalarını kopyala
COPY . .

# Laravel kurulumu ve optimizasyonları
RUN cp .env.example .env \
    && composer dump-autoload --optimize \
    && php artisan key:generate --force \
    && php artisan optimize \
    && php artisan view:cache \
    && php artisan config:cache

# Laravel için storage ve bootstrap/cache izinlerini ayarla
RUN chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

# Start services
CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
