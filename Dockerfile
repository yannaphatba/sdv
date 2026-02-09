FROM php:8.3-fpm-alpine

# Install Nginx
RUN apk add --no-cache \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    nginx
    
# Install PDO MySQL
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) pdo pdo_mysql gd zip bcmath

# Copy Nginx config
COPY nginx/default.conf /etc/nginx/http.d/default.conf

# Create directory structure matching URL path
RUN mkdir -p /var/www/html/sdv

# Copy application code
WORKDIR /var/www/html/sdv
COPY . .

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs


# Ensure permissions
RUN chown -R www-data:www-data /var/www/html

# Script to start both Nginx and PHP-FPM
RUN echo "#!/bin/sh" > /start.sh && \
    echo "php-fpm -D" >> /start.sh && \
    echo "nginx -g 'daemon off;'" >> /start.sh && \
    chmod +x /start.sh

CMD ["/start.sh"]
