FROM php:8.4-fpm
#ENV TZ='Europe/Berlin'
RUN ln -snf /usr/share/zoneinfo/Europe/Berlin /etc/localtime && echo Europe/Berlin > /etc/timezone

# System-Abhängigkeiten
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libpng-dev libonig-dev libxml2-dev mariadb-client \
    autoconf make gcc g++ \
    && docker-php-ext-install pdo pdo_mysql mbstring zip gd \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer installieren
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# php.ini
COPY php.ini /usr/local/etc/php/

# xdebug.ini
COPY xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www/html

# PHP-FPM User anpassen
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

# Composer Dependencies installieren (wird überschrieben wenn src gemountet wird)
# COPY src/composer.json src/composer.lock* ./
# RUN composer install --no-scripts --no-autoloader

# Autoloader generieren
# RUN composer dump-autoload --optimize

# Permissions setzen
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# USER www-data

EXPOSE 9000

CMD ["php-fpm"]
