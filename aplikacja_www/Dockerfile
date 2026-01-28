FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    zip unzip \
    && docker-php-ext-install pdo_pgsql mysqli

COPY . /var/www/html
WORKDIR /var/www/html

COPY nginx.conf /etc/nginx/nginx.conf

CMD service php8.2-fpm start && nginx -g 'daemon off;'
