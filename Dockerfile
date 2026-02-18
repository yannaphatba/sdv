FROM php:8.2-fpm-alpine

# Install extensions
RUN docker-php-ext-install pdo pdo_mysql

# Install nginx
RUN apk add --no-cache nginx

# Copy nginx config
COPY nginx/default.conf /etc/nginx/http.d/default.conf

# Create sdv folder inside container
RUN mkdir -p /var/www/html/sdv

# Set working directory
WORKDIR /var/www/html/sdv

# Copy project files
COPY . .

# Permissions
RUN chown -R www-data:www-data /var/www/html

# Start script
RUN echo "#!/bin/sh" > /start.sh && \
    echo "php-fpm -D" >> /start.sh && \
    echo "nginx -g 'daemon off;'" >> /start.sh && \
    chmod +x /start.sh

CMD ["/start.sh"]
