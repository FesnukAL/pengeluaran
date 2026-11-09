#!/bin/bash
set -e

# Setup Apache port dynamically based on Railway/Render $PORT env (defaults to 80 if not set)
PORT=${PORT:-80}
sed -i "s/Listen 80/Listen $PORT/g" /etc/apache2/ports.conf
sed -i "s/:80>/:$PORT>/g" /etc/apache2/sites-available/000-default.conf

# Buat symbolic link storage jika belum ada
php artisan storage:link || true

# Run database migrations automatically on startup
php artisan migrate --force || true

# Cache configurations if APP_KEY is provided
if [ -n "$APP_KEY" ]; then
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Pastikan environment variable Apache dimuat
. /etc/apache2/envvars
exec /usr/sbin/apache2 -DFOREGROUND
