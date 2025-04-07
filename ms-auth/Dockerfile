FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    curl zip unzip git supervisor \
    libpq-dev libonig-dev libxml2-dev libzip-dev \
    libcurl4-openssl-dev pkg-config libssl-dev \
    nginx \
    netcat-traditional \
    && docker-php-ext-install pdo pdo_mysql mbstring zip exif pcntl bcmath sockets \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && mkdir -p /usr/src/php/ext/swoole \
    && curl -fsSL https://pecl.php.net/get/swoole | tar xvz -C /usr/src/php/ext/swoole --strip 1 \
    && docker-php-ext-configure swoole --enable-openssl=no --enable-brotli=no \
    && docker-php-ext-install swoole

COPY --from=composer:2.8 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

RUN mkdir -p /var/log/supervisor

COPY supervisord/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

COPY . /var/www

COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

RUN chown -R www-data:www-data /var/www \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

EXPOSE 80 9000

ENTRYPOINT ["docker-entrypoint.sh"]

