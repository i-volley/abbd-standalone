FROM php:8.3-cli-alpine

RUN apk add --no-cache \
    nodejs npm git unzip curl \
    libpng-dev libjpeg-turbo-dev freetype-dev icu-dev libzip-dev oniguruma-dev libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo_mysql mbstring gd intl zip bcmath opcache xml

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN composer install --no-dev --optimize-autoloader \
    && npm ci \
    && npm run build \
    && php artisan config:cache \
    && php artisan route:cache \
    && php artisan view:cache

COPY docker-start.sh /app/docker-start.sh
RUN chmod +x /app/docker-start.sh

EXPOSE 8080

CMD ["/bin/sh", "/app/docker-start.sh"]
