# 素PHP版 会員管理システム（PHP 8.0 + Apache）
FROM php:8.0-apache

RUN apt-get update && apt-get install -y \
        libpng-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        msmtp \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install mysqli pdo_mysql gd mbstring \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# mail() を MailHog へ飛ばす（msmtp 経由）
RUN printf 'account default\nhost mailhog\nport 1025\nfrom noreply@example.com\nauth off\ntls off\n' > /etc/msmtprc \
    && echo "sendmail_path = /usr/bin/msmtp -t" > /usr/local/etc/php/conf.d/zz-mail.ini \
    && { echo "upload_max_filesize=10M"; echo "post_max_size=12M"; } > /usr/local/etc/php/conf.d/zz-upload.ini

# ドキュメントルートを public/ に
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri 's!/var/www/html!/var/www/html/public!g' \
        /etc/apache2/sites-available/*.conf \
        /etc/apache2/apache2.conf \
        /etc/apache2/conf-available/*.conf
