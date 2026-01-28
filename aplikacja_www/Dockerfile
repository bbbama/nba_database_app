FROM php:8.2-apache

# Instalacja PDO PostgreSQL
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && a2enmod rewrite

# Ustaw katalog aplikacji
WORKDIR /var/www/html

# Kopiujemy wszystkie pliki aplikacji
COPY . .

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

EXPOSE 80
