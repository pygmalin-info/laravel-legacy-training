#ララベル8想定のPHP8.0-FPM環境
FROM php:8.0-fpm

#必要なシステムライブラリ
RUN apt-get update && apt-get install -y \
        git \
        unzip \
        libzip-dev \
        libpng-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        default-mysql-client \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        zip \
        gd \
        bcmath \
        exif \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Composerを同梱
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# アップロード上限などを少し緩める？（教材用）
RUN { \
        echo "upload_max_filesize=10M"; \
        echo "post_max_size=12M"; \
        echo "memory_limit=256M"; \
    } > /usr/local/etc/php/conf.d/zz-training.ini
