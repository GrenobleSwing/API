FROM php:5.6-apache

RUN apt-get update

RUN apt-get install libxml2 libxml2-dev xml-core

# Install PDO MySQL driver
# See https://github.com/docker-library/php/issues/62
RUN docker-php-ext-install pdo pdo_mysql mysql mysqli
RUN docker-php-ext-install ctype
RUN docker-php-ext-install iconv
RUN docker-php-ext-install json
RUN docker-php-ext-install tokenizer
RUN docker-php-ext-install xml

RUN apt-get install -y libmcrypt-dev
RUN docker-php-ext-install mcrypt

RUN docker-php-ext-install dom

RUN apt-get install -y imagemagick

RUN apt-get install -y libcurl4-openssl-dev
RUN docker-php-ext-install curl

RUN apt-get install -y libicu-dev

RUN docker-php-ext-install intl
RUN docker-php-ext-install opcache
RUN docker-php-ext-install mbstring

RUN apt-get install -y libpng12-dev
RUN docker-php-ext-install gd

RUN apt-get install -y php5-apcu

# Set timezone
RUN rm /etc/localtime
RUN ln -s /usr/share/zoneinfo/Europe/Paris /etc/localtime
RUN "date"
COPY symfony.ini /usr/local/etc/php/conf.d/

RUN usermod -u 1000 www-data

RUN mkdir -p /var/www/html/var/jwt
RUN openssl genrsa -out /var/www/html/var/jwt/private.pem -aes256 -passout pass:untruc 4096
RUN openssl rsa -pubout -in /var/www/html/var/jwt/private.pem -out /var/www/html/var/jwt/public.pem -passin pass:untruc

# Enable Apache mod_rewrite
RUN a2enmod rewrite

EXPOSE 80

