# Build stage - PHP dependencies (use PHP 8.3 with required extensions)
FROM php:8.3-cli-alpine AS composer
RUN apk add --no-cache \
    libpng-dev libjpeg-turbo-dev freetype-dev libzip-dev icu-dev oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) bcmath intl gd pdo pdo_mysql zip \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
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
# Skip ~2min npm build if pre-built assets exist (avoids Railway timeout)
RUN if [ ! -f public/mix-manifest.json ]; then npm run production 2>/dev/null || npm run prod 2>/dev/null || npm run build 2>/dev/null || true; fi

# Final stage - nginx + PHP-FPM (with PostgreSQL support)
FROM richarvey/nginx-php-fpm:3.1.6
RUN apk add --no-cache postgresql-dev \
    && docker-php-ext-install pdo_pgsql \
    && apk del postgresql-dev
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

# Copy and prepare startup script (DB install + nginx)
COPY scripts/render-build.sh /scripts/startup.sh
RUN chmod +x /scripts/startup.sh

# Storage link only (chown deferred to startup to avoid timeout)
RUN php artisan storage:link 2>/dev/null || true

# Run install/migrate before starting nginx
CMD ["/bin/sh", "-c", "/scripts/startup.sh && exec /start.sh"]
