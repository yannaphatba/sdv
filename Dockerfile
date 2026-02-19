FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    nginx \
    curl \
    libzip \
    freetype \
    libpng \
    libjpeg-turbo \
    libzip-dev \
    freetype-dev \
    libpng-dev \
    libjpeg-turbo-dev

RUN docker-php-ext-configure gd --with-freetype --with-jpeg && \
    docker-php-ext-install gd zip pdo pdo_mysql

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY nginx/default.conf /etc/nginx/http.d/default.conf

WORKDIR /var/www/html
COPY src/ /var/www/html/src/

RUN cd /var/www/html/src && composer install --no-dev --optimize-autoloader --no-interaction --no-scripts --ignore-platform-reqs

RUN chown -R www-data:www-data /var/www/html

RUN rm -f /var/www/html/src/.env

RUN echo "#!/bin/sh" > /start.sh && \
    echo "if [ ! -d /var/www/html/src/vendor ] && [ -d /var/www/html/vendor ]; then ln -s /var/www/html/vendor /var/www/html/src/vendor; fi" >> /start.sh && \
    echo "if [ ! -f /var/www/html/src/vendor/autoload.php ]; then cd /var/www/html/src && composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs; fi" >> /start.sh && \
    echo "php-fpm -D" >> /start.sh && \
    echo "nginx -g 'daemon off;'" >> /start.sh && \
    chmod +x /start.sh

CMD ["/start.sh"]
