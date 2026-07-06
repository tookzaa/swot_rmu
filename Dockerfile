FROM node:20-alpine AS assets
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY . .
RUN npm run build

FROM php:8.4-fpm

RUN apt-get update && apt-get install -y \
    git \
    curl \
    unzip \
    nginx \
    supervisor \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libonig-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        xml \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .
COPY --from=assets /app/public/build ./public/build

RUN composer install --no-dev --optimize-autoloader --no-interaction

RUN mkdir -p /run/php \
    && printf '[www]\nlisten = /run/php/php-fpm.sock\nlisten.owner = www-data\nlisten.group = www-data\nlisten.mode = 0660\n' \
        > /usr/local/etc/php-fpm.d/zzz-socket.conf

COPY docker/production/nginx.conf /etc/nginx/sites-enabled/default
COPY docker/production/supervisord.conf /etc/supervisor/conf.d/supervisord.conf
COPY docker/production/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

ENTRYPOINT ["entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
