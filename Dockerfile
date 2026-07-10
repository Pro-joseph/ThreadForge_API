FROM php:8.4-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    libsqlite3-dev \
    libonig-dev \
    libzip-dev \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_sqlite mbstring zip

RUN pecl install redis \
    && docker-php-ext-enable redis

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
    storage/logs \
    bootstrap/cache


EXPOSE 9000

CMD ["php-fpm"]
