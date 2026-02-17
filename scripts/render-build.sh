#!/bin/bash
set -e

cd /var/www/html

# Ensure writable directories (moved from Dockerfile to avoid build timeout)
chown -R nginx:nginx storage bootstrap/cache 2>/dev/null || true
chmod -R 775 storage bootstrap/cache 2>/dev/null || true

# Railway uses PORT env - make nginx listen on it (must respond to pass health check)
if [ -n "$PORT" ]; then
  sed -i "s/listen 80/listen $PORT/" /etc/nginx/sites-available/default.conf 2>/dev/null || true
fi

# Start nginx FIRST so Railway health check passes (app must respond on PORT)
# Install runs in foreground - nginx handles requests (may show install/503 briefly)
echo "Starting nginx in background..."
/start.sh &
NGINX_PID=$!
sleep 5
echo "Nginx started (PID $NGINX_PID)"

# Get DB params - DATABASE_URL format: postgres://user:pass@host:port/dbname (Railway & Render)
if [ -n "$DATABASE_URL" ]; then
  DB_HOST=$(php -r "echo parse_url(getenv('DATABASE_URL'), PHP_URL_HOST) ?: 'localhost';")
  DB_PORT=$(php -r "echo parse_url(getenv('DATABASE_URL'), PHP_URL_PORT) ?: '5432';")
  DB_NAME=$(php -r "\$p=parse_url(getenv('DATABASE_URL'), PHP_URL_PATH); echo \$p ? trim(\$p,'/') : '';")
  DB_USER=$(php -r "echo parse_url(getenv('DATABASE_URL'), PHP_URL_USER) ?: '';")
  DB_PASS=$(php -r "echo parse_url(getenv('DATABASE_URL'), PHP_URL_PASS) ?: '';")
else
  DB_HOST=${DB_HOST:-localhost}
  DB_PORT=${DB_PORT:-5432}
  DB_NAME=${DB_DATABASE}
  DB_USER=${DB_USERNAME}
  DB_PASS=${DB_PASSWORD}
fi

# Run migrations or full install (nginx already serving - may show errors until install done)
if [ "$APP_INSTALLED" = "true" ]; then
  echo "Running migrations..."
  php artisan migrate --force
else
  echo "Running Akaunting install..."
  php artisan install \
    --db-host="$DB_HOST" \
    --db-port="$DB_PORT" \
    --db-name="$DB_NAME" \
    --db-username="$DB_USER" \
    --db-password="$DB_PASS" \
    --db-prefix="${DB_PREFIX:-ak_}" \
    --company-name="${COMPANY_NAME:-My Company}" \
    --company-email="${COMPANY_EMAIL:-admin@example.com}" \
    --admin-email="${ADMIN_EMAIL:-admin@example.com}" \
    --admin-password="${ADMIN_PASSWORD:-admin}" \
    --locale="${APP_LOCALE:-en-GB}" \
    --no-interaction
fi

# Cache config for production
php artisan config:cache
php artisan route:cache 2>/dev/null || true
php artisan view:cache 2>/dev/null || true

echo "Install complete. Nginx running as PID 1."
wait $NGINX_PID
