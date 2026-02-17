FROM php:8.3-fpm-alpine

# ติดตั้ง Extension ที่จำเป็น
RUN apk add --no-cache libpng-dev libjpeg-turbo-dev freetype-dev libzip-dev zip unzip nginx
RUN docker-php-ext-install pdo pdo_mysql gd zip bcmath

# --- จุดที่ต้องแก้ให้ละเอียด ---
# 1. ตั้งค่า WORKDIR ไปที่จุดที่แอปจะรันจริง
WORKDIR /var/www/html/src

# 2. ก๊อปปี้เฉพาะเนื้อหาในโฟลเดอร์ src มาวางที่ /var/www/html/src
# เพื่อให้ไฟล์ .env, artisan, และ composer.json อยู่ชั้นเดียวกันเป๊ะๆ
COPY src/ .

# 3. ก๊อปปี้คอนฟิก Nginx (อันนี้อยู่นอก src ต้องระบุ path ให้ถูก)
COPY nginx/default.conf /etc/nginx/http.d/default.conf

# 4. ติดตั้ง Composer (รันใน WORKDIR /var/www/html/src)
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# 5. ตั้งค่า Permissions ให้ Laravel เขียนไฟล์ได้
RUN mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache bootstrap/cache
RUN chown -R www-data:www-data /var/www/html/src/storage /var/www/html/src/bootstrap/cache
RUN chmod -R 775 /var/www/html/src/storage /var/www/html/src/bootstrap/cache

# 6. สร้าง Symbolic Link
RUN php artisan storage:link

# 7. Start Script
RUN echo "#!/bin/sh" > /start.sh && \
    echo "php-fpm -D" >> /start.sh && \
    echo "nginx -g 'daemon off;'" >> /start.sh && \
    chmod +x /start.sh

CMD ["/start.sh"]