FROM php:8.4-cli

RUN apt-get update && apt-get install -y git unzip && \
    git clone https://github.com/jokkedk/webgrind.git /var/www/webgrind && \
    rm -rf /var/lib/apt/lists/* && \
    sed -i "s|define('STORAGE_DIR', .*|define('STORAGE_DIR', '/var/www/storage/logs');|" /var/www/webgrind/config.php

EXPOSE 8080

WORKDIR /var/www/webgrind

CMD ["php", "-S", "0.0.0.0:8080", "-t", "/var/www/webgrind"]
