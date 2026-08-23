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

# انسخ ملفات composer الأول (للـ cache)
COPY composer.json composer.lock ./

# ثبّت الحزم مع حد ذاكرة مرفوع وبدون scripts
RUN COMPOSER_MEMORY_LIMIT=-1 composer install \
    --no-dev --no-scripts --no-autoloader --no-interaction --prefer-dist

# انسخ باقي المشروع
COPY . /app

# ولّد الـ autoloader بعد نسخ كل الملفات
RUN COMPOSER_MEMORY_LIMIT=-1 composer dump-autoload --optimize --no-dev

RUN php artisan config:clear

EXPOSE 8080
CMD php artisan migrate:fresh --seed --force && php artisan serve --host=0.0.0.0 --port=${PORT:-8080}