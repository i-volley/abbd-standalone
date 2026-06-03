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

# ── Seed: SOLO se SEED_ON_DEPLOY=true nelle variabili Railway ─────────────────
# In produzione con MySQL persistente il seed NON deve girare automaticamente.
# Per inizializzare un DB vuoto: imposta SEED_ON_DEPLOY=true in Railway →
# deploy → rimuovi la variabile. I dati utente non vengono mai toccati.
if [ "$SEED_ON_DEPLOY" = "true" ]; then
    echo "Seed: SEED_ON_DEPLOY=true rilevato, eseguo seed..."
    php artisan db:seed --force && echo "Seed OK" || echo "Seed WARNING"
    echo "ATTENZIONE: rimuovi SEED_ON_DEPLOY dalle variabili Railway dopo questo deploy."
else
    echo "Seed: skip — SEED_ON_DEPLOY non impostato, dati utente preservati."
fi

exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
