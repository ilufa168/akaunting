# Build stage - PHP dependencies
FROM composer:2 AS composer
WORKDIR /app

# Copy composer files and modules (needed for custom install paths)
COPY composer.json composer.lock* ./
COPY modules ./modules

RUN composer install --no-dev --no-scripts --prefer-dist

COPY . .
RUN composer dump-autoload --optimize

# Build stage - Node assets
FROM node:18-alpine AS node
WORKDIR /app

COPY package.json package-lock.json* yarn.lock* ./
RUN npm ci 2>/dev/null || npm install

COPY --from=composer /app /app
RUN npm run production 2>/dev/null || npm run prod 2>/dev/null || (npm run build 2>/dev/null; true)

# Final stage - nginx + PHP-FPM (with PostgreSQL support added)
FROM richarvey/nginx-php-fpm:3.1.6
RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo_pgsql \
    && apk del postgresql-dev || true
WORKDIR /var/www/html

COPY --from=node /app .

# Image config
ENV SKIP_COMPOSER=1
ENV WEBROOT=/var/www/html/public
ENV PHP_ERRORS_STDERR=1
ENV RUN_SCRIPTS=1
ENV REAL_IP_HEADER=1
ENV COMPOSER_ALLOW_SUPERUSER=1

# Laravel config
ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr

# Create storage link and ensure writable directories
RUN php artisan storage:link 2>/dev/null || true && \
    chown -R nginx:nginx storage bootstrap/cache && \
    chmod -R 775 storage bootstrap/cache

CMD ["/start.sh"]
