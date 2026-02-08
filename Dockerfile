FROM php:8.3-fpm

# ตั้งค่า Folder ทำงาน
WORKDIR /var/www

# ติดตั้งโปรแกรมพื้นฐานที่จำเป็น (เพิ่ม libzip-dev แล้ว)
RUN apt-get update && apt-get install -y \
    build-essential \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    locales \
    zip \
    jpegoptim optipng pngquant gifsicle \
    vim \
    unzip \
    git \
    curl \
    libonig-dev \
    libzip-dev

# ล้าง Cache เพื่อลดขนาดไฟล์
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# ติดตั้ง Extension PHP (เพิ่ม zip แล้ว)
RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

# ติดตั้ง Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# สร้าง User สำหรับรัน (เพื่อไม่ให้ติด Permission)
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www

# Copy folder งาน
COPY . /var/www
COPY --chown=www:www . /var/www

# เปลี่ยน User เป็น www
USER www

# เปิด Port 9000
EXPOSE 9000

CMD ["php-fpm"]