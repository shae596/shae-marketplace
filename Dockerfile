FROM php:8.3-cli-bookworm

RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    mariadb-server \
    default-mysql-client \
    libzip-dev \
    libpng-dev \
    libjpeg62-turbo-dev \
    libfreetype6-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        zip \
        gd \
        bcmath \
    && rm -rf /var/lib/apt/lists/* \
    && mkdir -p /var/lib/mysql /var/run/mysqld \
    && chown -R mysql:mysql /var/lib/mysql /var/run/mysqld

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --prefer-dist --no-interaction

COPY . .

RUN composer install --no-dev --optimize-autoloader --no-interaction \
    && chmod +x docker/render-start.sh \
    && mkdir -p storage/framework/{cache,sessions,views} storage/logs storage/app/public/products \
    && chown -R www-data:www-data storage bootstrap/cache || true

ENV PORT=10000
EXPOSE 10000

CMD ["/bin/bash", "docker/render-start.sh"]
