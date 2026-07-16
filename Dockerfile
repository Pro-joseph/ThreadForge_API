FROM php:8.4-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    unzip \
    libsqlite3-dev \
    libonig-dev \
    libzip-dev \
    && docker-php-ext-configure zip --with-libzip \
    && docker-php-ext-install pdo_sqlite mbstring zip \
    && pecl install redis && docker-php-ext-enable redis \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --no-dev \
    --no-scripts

COPY . .

RUN mkdir -p storage/framework/{cache,sessions,testing,views} \
    storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

USER www-data

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["php-fpm"]
