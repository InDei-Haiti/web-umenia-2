FROM php:5.6-fpm

RUN apt-get update -y && apt-get install -y \
	libmcrypt-dev \
	libpng-dev

RUN docker-php-ext-install \
	pdo_mysql \
	gd \
	mcrypt \
	zip

RUN chown -R www-data:www-data /var/www

# # Install php dependencies
COPY --from=composer:1.5 /usr/bin/composer /usr/bin/composer
# Install app dependencies
COPY composer.json /var/www/composer.json
COPY database /var/www/database
COPY tests/TestCase.php /var/www/tests/TestCase.php

WORKDIR /var/www

RUN composer install --no-plugins --no-scripts --no-interaction
