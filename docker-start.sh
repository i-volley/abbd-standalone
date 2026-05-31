#!/bin/sh
set -e

# Fallback APP_KEY se non impostato nelle env vars di Railway
if [ -z "$APP_KEY" ]; then
    export APP_KEY="base64:72/KL/qkEeS+KQE31iqfLqsq0e6bNqGVFaU39Rywxkc="
fi

# Crea .env minimale se non esiste (Laravel richiede file .env o variabili d'ambiente)
if [ ! -f .env ]; then
    cp .env.example .env 2>/dev/null || touch .env
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force
php artisan db:seed --force
php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
