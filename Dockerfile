FROM php:8.2-fpm-alpine

# Install PDO MySQL
RUN docker-php-ext-install pdo pdo_mysql

# Install Nginx
RUN apk add --no-cache nginx

# Copy Nginx config
COPY nginx/default.conf /etc/nginx/http.d/default.conf

# Create directory structure matching URL path
RUN mkdir -p /var/www/html/sdv

# Copy application code
WORKDIR /var/www/html/sdv
COPY . .

# Ensure permissions
RUN chown -R www-data:www-data /var/www/html

# Script to start both Nginx and PHP-FPM
RUN echo "#!/bin/sh" > /start.sh && \
    echo "php-fpm -D" >> /start.sh && \
    echo "nginx -g 'daemon off;'" >> /start.sh && \
    chmod +x /start.sh

CMD ["/start.sh"]
