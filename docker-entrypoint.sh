#!/bin/sh
set -e

if [ ! -f .env ]; then
    echo "Copying .env.example to .env..."
    cp .env.example .env
fi

if [ -z "$APP_KEY" ] || [ "$APP_KEY" = "base64:" ]; then
    echo "Generating APP_KEY..."
    php artisan key:generate --force
fi

echo "Running migrations..."
php artisan migrate --force

echo "Running package:discover..."
php artisan package:discover --ansi 2>/dev/null || true

exec docker-php-entrypoint "$@"
