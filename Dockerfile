FROM php:8.4-fpm

WORKDIR /var/www

RUN apt-get update && apt-get install -y \
    curl \
    unzip \
    git \
    libsqlite3-dev \
    libonig-dev \
    libzip-dev \
    && docker-php-ext-configure zip --with-libzip \
    && docker-php-ext-install pdo_sqlite mbstring zip \
    && rm -rf /var/lib/apt/lists/*

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# copy only composer files first for layer caching
COPY composer.json composer.lock ./
RUN composer install \
    --optimize-autoloader \
    --no-interaction \
    --no-progress \
    --no-dev

# copy application
COPY . .

EXPOSE 9000

CMD ["php-fpm"]