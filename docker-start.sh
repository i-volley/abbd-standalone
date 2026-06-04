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

# ── Seed sicuro: crea SOLO utente demo se non esiste ────────────────────────
# Gira SEMPRE ma è 100% idempotente (firstOrCreate).
# Non tocca team, stagioni, sedute o altri dati reali dell'allenatore.
php artisan demo:seed && echo "Demo seed OK" || echo "Demo seed WARNING"

# ── Seed lookup tables (ruoli, sport, parametri) ─────────────────────────────
php artisan db:seed --class=RoleSeeder --force
php artisan db:seed --class=SportSeeder --force
php artisan db:seed --class=ParametroEsercizioSeeder --force

exec php artisan serve --host=0.0.0.0 --port=${PORT:-8080}
