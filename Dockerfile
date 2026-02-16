FROM php:8.3-fpm-alpine
RUN apk add --no-cache libpng-dev libjpeg-turbo-dev freetype-dev libzip-dev zip unzip nginx
RUN docker-php-ext-install pdo pdo_mysql gd zip bcmath

# --- แก้จุดนี้ครับ จากเดิม /var/www/html ให้เติม /src เข้าไป ---
WORKDIR /var/www/html/src 

# 1. ก๊อปปี้ทุกอย่าง (เพื่อให้ Dockerfile ที่อยู่นอกสุด ก๊อปโฟลเดอร์ src เข้าไปด้วย)
COPY . /var/www/html

# 2. ก๊อปปี้คอนฟิก Nginx (อันนี้อยู่ที่เดิม)
COPY nginx/default.conf /etc/nginx/http.d/default.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ตอนนี้ WORKDIR เราอยู่ที่ /var/www/html/src แล้ว มันจะเจอไฟล์ artisan แน่นอน
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# --- จุดสำคัญ: เชื่อมรูปภาพ นศ. ---
RUN php artisan storage:link

RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache bootstrap/cache && \
    chown -R www-data:www-data /var/www/html/src && \
    chmod -R 775 storage bootstrap/cache

RUN echo "#!/bin/sh" > /start.sh && \
    echo "php-fpm -D" >> /start.sh && \
    echo "nginx -g 'daemon off;'" >> /start.sh && \
    chmod +x /start.sh

CMD ["/start.sh"]