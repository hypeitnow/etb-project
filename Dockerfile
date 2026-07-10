FROM node:20-alpine AS frontend

WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci

COPY . .
RUN npm run build

FROM dunglas/frankenphp:1-php8.3-alpine

RUN install-php-extensions \
    pdo_pgsql \
    intl \
    bcmath \
    opcache

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

CMD ["frankenphp", "run", "--config", "/app/Caddyfile"]
