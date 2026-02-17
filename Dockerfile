FROM php:8.3-fpm-alpine

# ติดตั้ง Extension ที่จำเป็น
RUN apk add --no-cache libpng-dev libjpeg-turbo-dev freetype-dev libzip-dev zip unzip nginx
RUN docker-php-ext-install pdo pdo_mysql gd zip bcmath

# 1. ตั้งค่า WORKDIR ไปที่โฟลเดอร์ src เลยครับ (เพราะไฟล์ artisan อยู่ในนี้)
WORKDIR /var/www/html/src

# 2. ก๊อปปี้ไฟล์ทั้งหมดเข้าไปที่ /var/www/html 
# (โครงสร้างจะเป็น /var/www/html/src, /var/www/html/nginx เป็นต้น)
COPY . /var/www/html

# 3. ก๊อปปี้คอนฟิก Nginx
COPY nginx/default.conf /etc/nginx/http.d/default.conf

# 4. ติดตั้ง Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# 5. แก้ไขการตั้งสิทธิ์ (Permissions) ให้ครอบคลุม
# ต้องสร้างโฟลเดอร์ให้ครบก่อนเปลี่ยนสิทธิ์ครับ
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/src/storage /var/www/html/src/bootstrap/cache
RUN chmod -R 775 /var/www/html/src/storage /var/www/html/src/bootstrap/cache

# 6. สร้างลิงก์เก็บรูปภาพ (ต้องรันใน WORKDIR ที่มีไฟล์ artisan)
RUN php artisan storage:link

# 7. Script สำหรับรันระบบ
RUN echo "#!/bin/sh" > /start.sh && \
    echo "php-fpm -D" >> /start.sh && \
    echo "nginx -g 'daemon off;'" >> /start.sh && \
    chmod +x /start.sh

CMD ["/start.sh"]