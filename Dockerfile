FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

FROM php:8.3-cli-alpine

RUN apk add --no-cache \
    libpq-dev \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    && docker-php-ext-install pdo_pgsql intl bcmath opcache mbstring zip

COPY . /app

WORKDIR /app

COPY --from=frontend /app/public/build /app/public/build

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --no-interaction --optimize-autoloader

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_LEVEL=warning

CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8080"]
