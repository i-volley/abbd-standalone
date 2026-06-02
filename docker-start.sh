#!/bin/sh

# ── APP KEY fallback ─────────────────────────────────────────────────────────
if [ -z "$APP_KEY" ]; then
    export APP_KEY="base64:72/KL/qkEeS+KQE31iqfLqsq0e6bNqGVFaU39Rywxkc="
fi

# ── DATABASE: Railway MySQL → variabili Laravel ──────────────────────────────
# Railway inietta automaticamente MYSQLHOST, MYSQLPORT, MYSQLDATABASE,
# MYSQLUSER, MYSQLPASSWORD quando colleghi un servizio MySQL al progetto.
if [ -n "$MYSQLHOST" ]; then
    export DB_CONNECTION=mysql
    export DB_HOST=$MYSQLHOST
    export DB_PORT=${MYSQLPORT:-3306}
    export DB_DATABASE=$MYSQLDATABASE
    export DB_USERNAME=$MYSQLUSER
    export DB_PASSWORD=$MYSQLPASSWORD
    echo "DB: MySQL @ $DB_HOST:$DB_PORT/$DB_DATABASE"
else
    # Fallback SQLite (locale / dev)
    export DB_CONNECTION=sqlite
    mkdir -p /app/database
    [ -f /app/database/database.sqlite ] || touch /app/database/database.sqlite
    echo "DB: SQLite (fallback — nessun servizio MySQL rilevato)"
fi

# ── Laravel cache ────────────────────────────────────────────────────────────
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# ── Migrate ──────────────────────────────────────────────────────────────────
php artisan migrate --force && echo "Migrate OK" || echo "Migrate WARNING"

# ── Seed SOLO al primo avvio (DB vuoto) ──────────────────────────────────────
# Con MySQL persistente il seed non deve girare ad ogni deploy: cancellerebbe
# i dati reali inseriti dall'utente. Gira solo se non ci sono ancora utenti.
HAS_USERS=$(php artisan tinker --execute="echo App\Models\User::count();" 2>/dev/null | tail -1 | tr -d '[:space:]')
if [ -z "$HAS_USERS" ] || [ "$HAS_USERS" = "0" ]; then
    echo "Seed: DB vuoto, eseguo seed iniziale..."
    php artisan db:seed --force && echo "Seed OK" || echo "Seed WARNING"
else
    echo "Seed: skip — DB ha già $HAS_USERS utenti, dati preservati"
fi

exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
