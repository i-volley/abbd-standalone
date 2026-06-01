#!/bin/sh

# Fallback APP_KEY se Railway non ha impostato la variabile
if [ -z "$APP_KEY" ]; then
    export APP_KEY="base64:72/KL/qkEeS+KQE31iqfLqsq0e6bNqGVFaU39Rywxkc="
fi

# Garantisce che il file SQLite esista (necessario quando /app/database è un
# volume Railway montato vuoto al primo avvio — abilita la persistenza dati)
mkdir -p /app/database
[ -f /app/database/database.sqlite ] || touch /app/database/database.sqlite

# Laravel legge direttamente dalle env vars del container — nessun .env file necessario
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan migrate --force && echo "Migrate OK" || echo "Migrate warning"
php artisan db:seed --force && echo "Seed OK" || echo "Seed warning"
exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
