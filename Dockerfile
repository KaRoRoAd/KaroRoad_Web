# Użyj oficjalnie zalecanego obrazu PHP w wersji 8.3
FROM php:8.3-apache

# Instalacja zależności
RUN apt-get update -y && apt-get install -y \
    curl \
    zip \
    unzip \
    && docker-php-ext-install pdo pdo_mysql

# Ustawienie katalogu roboczego
WORKDIR /var/www

# Kopiowanie plików aplikacji do kontenera
COPY . .

# Kopiowanie pliku konfiguracji Apache
COPY ./docker/vhost.conf /etc/apache2/sites-available/000-default.conf



# Instalacja Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

# Ustawienie uprawnień do folderu
RUN chown -R www-data:www-data /var/www

# Włączenie modułu rewrite w Apache
RUN a2enmod rewrite

# Ustawienie uprawnień do skryptu entrypoint.sh


# Ustawienie zmiennej środowiskowej
ENV PORT=80

# Ustawienie domyślnego punktu wejścia

