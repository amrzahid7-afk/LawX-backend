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
COPY . /app

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN php artisan config:clear

EXPOSE 8080
CMD php artisan migrate:fresh --seed --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}