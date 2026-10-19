#!/bin/bash
set -e

# Setup Apache port dynamically based on Render's $PORT env (defaults to 80 if not set)
PORT=${PORT:-80}
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/<VirtualHost \*:80>/<VirtualHost \*:$PORT>/g" /etc/apache2/sites-available/000-default.conf

# Cache configurations if APP_KEY is provided
if [ -n "$APP_KEY" ]; then
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Run database migrations automatically on startup
php artisan migrate --force || true

exec apache2-foreground

