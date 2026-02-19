FROM php:8.3-fpm-alpine

RUN docker-php-ext-install pdo pdo_mysql

RUN apk add --no-cache nginx curl

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY nginx/default.conf /etc/nginx/http.d/default.conf

WORKDIR /var/www/html
COPY . .

RUN cd /var/www/html/src && composer install --no-dev --optimize-autoloader

RUN chown -R www-data:www-data /var/www/html

RUN rm -f /var/www/html/src/.env

RUN echo "#!/bin/sh" > /start.sh && \
    echo "php-fpm -D" >> /start.sh && \
    echo "nginx -g 'daemon off;'" >> /start.sh && \
    chmod +x /start.sh

CMD ["/start.sh"]
