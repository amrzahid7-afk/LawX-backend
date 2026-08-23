FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
    libreoffice libreoffice-writer \
    fonts-liberation fonts-dejavu fonts-noto \
    libzip-dev libpng-dev libonig-dev libcurl4-openssl-dev \
    unzip git curl \
    && docker-php-ext-install pdo pdo_mysql zip gd bcmath \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY composer.json composer.lock ./

RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
    --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist

COPY . /app

RUN COMPOSER_MEMORY_LIMIT=-1 composer dump-autoload --optimize --no-dev --no-scripts

RUN php artisan config:clear

EXPOSE 8080
CMD php artisan migrate:fresh --seed --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}