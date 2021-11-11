# docker/Dockerfile
FROM nginx:latest

# Configure non-root user.
ARG PUID=1000
ENV PUID ${PUID}
ARG PGID=1000
ENV PGID ${PGID}

RUN groupmod -o -g ${PGID} www-data && \
    usermod -o -u ${PUID} -g www-data www-data

RUN mkdir -p /var/www/public

#COPY ./docker/nginx/info_php.txt /usr/share/nginx/html/index.php
#COPY ./docker/php-fpm/info_php.txt /var/www/public/index.php

#RUN chown -R www-data:www-data /var/www

#COPY ./docker/nginx/nginx.conf /etc/nginx/nginx.conf
#RUN chmod 644 /etc/nginx/nginx.conf
COPY ./docker/nginx/default.conf /etc/nginx/conf.d/default.conf
RUN chmod 644 /etc/nginx/conf.d/default.conf

EXPOSE 8080
