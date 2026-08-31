FROM php:8.3-fpm-alpine

# Install ekstensi wajib dan pendukung CI4 & PhpSpreadsheet
RUN apk add --no-cache \
    icu-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libzip-dev \
    zip \
    unzip \
    git \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) \
    intl \
    pdo_mysql \
    mysqli \
    gd \
    zip \
    opcache \
    bcmath \
    exif

# Konfigurasi PHP (Upload limit, Memory limit, Execution time & Timezone)
RUN { \
    echo 'memory_limit = 512M'; \
    echo 'upload_max_filesize = 50M'; \
    echo 'post_max_size = 50M'; \
    echo 'max_execution_time = 300'; \
    echo 'date.timezone = Asia/Makassar'; \
 } > /usr/local/etc/php/conf.d/custom-php.ini

# Konfigurasi Opcache untuk development
RUN { \
    echo 'opcache.enable=1'; \
    echo 'opcache.memory_consumption=128'; \
    echo 'opcache.interned_strings_buffer=8'; \
    echo 'opcache.max_accelerated_files=4000'; \
    echo 'opcache.revalidate_freq=0'; \
    echo 'opcache.validate_timestamps=1'; \
    echo 'opcache.fast_shutdown=1'; \
 } > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Git safe directory untuk mengatasi dubious ownership saat mount volume
RUN git config --global --add safe.directory /var/www/html

WORKDIR /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer