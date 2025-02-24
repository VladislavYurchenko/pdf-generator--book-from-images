FROM php:8.1-fpm

# Устанавливаем зависимости
RUN apt-get update && apt-get install -y \
    libmagickwand-dev \
    && pecl install imagick \
    && docker-php-ext-enable imagick

# Настраиваем Imagick, разрешаем работу с PDF
RUN mkdir -p /etc/ImageMagick-6/ && echo '<?xml version="1.0" encoding="UTF-8"?><policymap><policy domain="coder" rights="read | write" pattern="PDF" /></policymap>' > /etc/ImageMagick-6/policy.xml

# Копируем настройки PHP
COPY php.ini /usr/local/etc/php/php.ini

# Устанавливаем права на папку с загрузками
RUN mkdir -p /var/www/html/uploads && chmod -R 777 /var/www/html/uploads

WORKDIR /var/www/html
CMD ["php-fpm"]