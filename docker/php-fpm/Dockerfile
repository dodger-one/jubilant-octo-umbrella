# docker/Dockerfile
FROM php:7.4-fpm

ARG APCU_VERSION=5.1.18

LABEL Maintainer="Oliver Adria <oliver@adria.dev>" \
      Description="Base setup for web development with PHP and PostgreSQL."

# Get frequently used tools
RUN apt-get update && apt-get install -y \
    build-essential \
    libicu-dev \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    libonig-dev \
    locales \
    zip \
    unzip \
    jpegoptim optipng pngquant gifsicle \
    libpq-dev \
    vim \
    git \
    curl \
    wget

RUN docker-php-ext-configure zip

RUN docker-php-ext-install \
        bcmath \
        mbstring \
        pcntl \
        intl \
        zip \
        opcache \
        pdo_pgsql

# apcu for caching, xdebug for debugging and also phpunit coverage
RUN pecl install \
        apcu-${APCU_VERSION} \
        xdebug \
    && docker-php-ext-enable \
        apcu \
        xdebug


RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Configure non-root user.
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

RUN groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data

RUN rm -fr /var/www/public
RUN mkdir /var/www/public
#COPY ./docker/app/info_php.txt /var/www/public/index.php
COPY ./docker/application/ /var/www/public/

RUN chown -R www-data:www-data /var/www

#COPY nginx_default.conf /etc/nginx/nginx.conf
#RUN chmod 644 /etc/nginx/nginx.conf
#COPY app.conf /etc/nginx/conf.d/default.conf
#RUN chmod 644 /etc/nginx/conf.d/default.conf

#EXPOSE 8080

CMD ["php-fpm"]
