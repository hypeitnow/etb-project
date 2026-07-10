FROM dunglas/frankenphp:1-php8.3-alpine

RUN install-php-extensions \
    pdo_pgsql \
    intl \
    bcmath \
    opcache

COPY . /app

WORKDIR /app

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN composer install --no-dev --no-interaction --optimize-autoloader

RUN npm ci && npm run build && rm -rf node_modules

RUN chown -R www-data:www-data storage bootstrap/cache

EXPOSE 8080

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_LEVEL=warning

CMD ["frankenphp", "run", "--config", "/app/Caddyfile"]
