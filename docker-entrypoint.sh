#!/bin/bash
set -e

# Default PORT 80 jika variabel $PORT dari Railway belum di-set
export PORT=${PORT:-80}

# Buat symbolic link storage jika belum ada
php artisan storage:link || true

# Run database migrations otomatis
php artisan migrate --force || true

# Cache configurations if APP_KEY is provided
if [ -n "$APP_KEY" ]; then
    php artisan config:cache || true
    php artisan route:cache || true
    php artisan view:cache || true
fi

# Jalankan script Apache resmi bawaan image Docker PHP
exec apache2-foreground
