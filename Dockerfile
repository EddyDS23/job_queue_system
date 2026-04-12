FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    libzip-dev \
    libexif-dev \
    libonig-dev \
    && rm -rf /var/lib/apt/list/*

RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl bcmath

RUN pecl install redis && docker-php-ext-enable redis

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

