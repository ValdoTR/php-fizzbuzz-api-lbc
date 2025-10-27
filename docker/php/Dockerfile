FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git unzip libicu-dev libzip-dev \
 && docker-php-ext-install intl zip opcache \
 && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

COPY . .

# Install dependencies
RUN composer install --no-interaction --prefer-dist --no-progress

# Ensure we can write in var/ and data/
RUN mkdir -p var/cache var/log && chown -R www-data:www-data var data

EXPOSE 9000
CMD ["php-fpm"]
