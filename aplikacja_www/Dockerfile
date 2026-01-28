# Startujemy od obrazu PHP z Apache
FROM php:8.2-apache

# Instalujemy PostgreSQL PDO i inne potrzebne rozszerzenia
RUN apt-get update && apt-get install -y \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && a2enmod rewrite

# Ustawiamy katalog roboczy aplikacji
WORKDIR /var/www/html

# Kopiujemy wszystkie pliki aplikacji
COPY . .

# Otwieramy port 80
EXPOSE 80
