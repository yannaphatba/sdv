FROM php:8.3-fpm-alpine
RUN apk add --no-cache libpng-dev libjpeg-turbo-dev freetype-dev libzip-dev zip unzip nginx
RUN docker-php-ext-install pdo pdo_mysql gd zip bcmath

# บอกหุ่นยนต์ว่า "บ้านของโค้ดเราอยู่ในโฟลเดอร์ src นะ"
WORKDIR /var/www/html/src

# 1. ก๊อปปี้ทุกอย่างจากข้างนอก (รวมถึงโฟลเดอร์ src ที่มีไฟล์ครบแล้ว) เข้าไปที่ /var/www/html
COPY . /var/www/html

# 2. ก๊อปปี้คอนฟิก Nginx จากพิกัด nginx/default.conf (ตามรูป image_a6bc83.png)
COPY nginx/default.conf /etc/nginx/http.d/default.conf

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# ตอนนี้ WORKDIR เราอยู่ใน src แล้ว มันจะเจอไฟล์ composer.json และ artisan แน่นอนครับริว!
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# --- จุดสำคัญ: เชื่อมโยงรูปภาพ นศ. (Profiles/Vehicles) ---
RUN php artisan storage:link

# ตั้งสิทธิ์ให้ระบบ "บันทึกรูปใหม่" และเข้าถึงไฟล์ได้
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache bootstrap/cache && \
    chown -R www-data:www-data /var/www/html/src && \
    chmod -R 775 storage bootstrap/cache
# ---------------------------------------

RUN echo "#!/bin/sh" > /start.sh && \
    echo "php-fpm -D" >> /start.sh && \
    echo "nginx -g 'daemon off;'" >> /start.sh && \
    chmod +x /start.sh

CMD ["/start.sh"]