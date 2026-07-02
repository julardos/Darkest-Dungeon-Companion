FROM php:8.2-cli-alpine

RUN apk add --no-cache git curl unzip libzip-dev \
    && docker-php-ext-install zip pdo pdo_sqlite

RUN curl -sS https://getcomposer.org/installer | php -- \
    --install-dir=/usr/local/bin --filename=composer

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install --no-dev --optimize-autoloader --no-interaction

COPY . .

RUN cp .env.example .env \
    && php artisan key:generate \
    && chmod -R 777 /app/storage /app/bootstrap/cache

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]
