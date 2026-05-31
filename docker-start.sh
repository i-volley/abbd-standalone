#!/bin/sh

# Fallback APP_KEY se non impostato
if [ -z "$APP_KEY" ]; then
    export APP_KEY="base64:72/KL/qkEeS+KQE31iqfLqsq0e6bNqGVFaU39Rywxkc="
fi

# .env file per compatibilità Laravel
if [ ! -f .env ]; then
    cp .env.example .env 2>/dev/null || touch .env
fi

php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force && echo "Migrate OK" || echo "Migrate warning"
php artisan db:seed --force && echo "Seed OK" || echo "Seed warning — proseguo"
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
