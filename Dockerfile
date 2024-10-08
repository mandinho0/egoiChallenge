FROM php:8.2-fpm

LABEL maintainer="getlaminas.org" \
    org.label-schema.docker.dockerfile="/Dockerfile" \
    org.label-schema.name="Laminas MVC Skeleton" \
    org.label-schema.url="https://docs.getlaminas.org/mvc/" \
    org.label-schema.vcs-url="https://github.com/laminas/laminas-mvc-skeleton"

## Update package information
RUN apt-get update

## Install Composer
RUN curl -sS https://getcomposer.org/installer \
  | php -- --install-dir=/usr/local/bin --filename=composer

### PHP Extensisons

## Install zip libraries and extension
RUN apt-get install --yes git zlib1g-dev libzip-dev \
    && docker-php-ext-install zip

## Install intl library and extension
RUN apt-get install --yes libicu-dev \
    && docker-php-ext-configure intl \
    && docker-php-ext-install intl

## Install MySQL PDO support
RUN apt-get install --yes default-mysql-client \
    && docker-php-ext-install pdo pdo_mysql mysqli \
    && docker-php-ext-enable pdo_mysql mysqli

## Install pcntl for asynchronous mode
RUN docker-php-ext-install pcntl

## Install OpenSSL
RUN apt-get install --yes libssl-dev

## Install Stomp for ActiveMQ
RUN pecl install stomp \
    && docker-php-ext-enable stomp

EXPOSE 80 

WORKDIR /var/www/html
