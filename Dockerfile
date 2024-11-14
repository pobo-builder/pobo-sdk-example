FROM php:8.3-fpm

RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libzip-dev \
    && docker-php-ext-install zip

WORKDIR /var/www

COPY . /var/www

EXPOSE 9000

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

CMD ["php-fpm"]