FROM php:8.1-fpm-alpine as build-stage

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
COPY . /app
WORKDIR /app
RUN composer install --no-dev --optimize-autoloader

FROM nginx:latest
COPY docker/nginx/default.conf /etc/nginx/conf.d/default.conf
COPY --from=build-stage /app /var/www/html