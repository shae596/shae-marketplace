#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

echo "[render] Boot SHAE..."

export DB_HOST="${DB_HOST:-localhost}"
export DB_DATABASE="${DB_DATABASE:-shae}"
export DB_USERNAME="${DB_USERNAME:-root}"
export DB_PASSWORD="${DB_PASSWORD:-}"
export SESSION_DRIVER="${SESSION_DRIVER:-file}"
export CACHE_STORE="${CACHE_STORE:-file}"

if [ -z "${APP_KEY:-}" ]; then
    echo "[render] ERROR: APP_KEY is missing. Add it in Render Environment."
    exit 1
fi

mkdir -p storage/framework/{cache,sessions,views} storage/logs storage/app/public/products
chmod -R 777 storage bootstrap/cache 2>/dev/null || true

start_embedded_mysql() {
    echo "[render] Starting MariaDB (MySQL)..."

    if [ ! -d /var/lib/mysql/mysql ]; then
        mariadb-install-db --user=mysql --datadir=/var/lib/mysql --auth-root-authentication-method=normal
    fi

    if ! pgrep -x mariadbd >/dev/null 2>&1; then
        mariadbd --user=mysql --datadir=/var/lib/mysql --bind-address=127.0.0.1 &
    fi

    for i in $(seq 1 60); do
        if mariadb-admin ping --silent 2>/dev/null; then
            echo "[render] MariaDB ready (${i}s)"
            break
        fi
        sleep 1
    done

    mariadb -u root <<-EOSQL
CREATE DATABASE IF NOT EXISTS \`${DB_DATABASE}\`;
CREATE USER IF NOT EXISTS '${DB_USERNAME}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
CREATE USER IF NOT EXISTS '${DB_USERNAME}'@'127.0.0.1' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON \`${DB_DATABASE}\`.* TO '${DB_USERNAME}'@'localhost';
GRANT ALL PRIVILEGES ON \`${DB_DATABASE}\`.* TO '${DB_USERNAME}'@'127.0.0.1';
FLUSH PRIVILEGES;
EOSQL
}

if [ "${DB_HOST}" = "127.0.0.1" ] || [ "${DB_HOST}" = "localhost" ]; then
    start_embedded_mysql
else
    echo "[render] External MySQL: ${DB_HOST}"
fi

echo "[render] Waiting for database (host=${DB_HOST}, user=${DB_USERNAME})..."
db_ready=0
for i in $(seq 1 45); do
    if php -r "
        \$h = getenv('DB_HOST') ?: 'localhost';
        \$p = getenv('DB_PORT') ?: '3306';
        \$d = getenv('DB_DATABASE') ?: 'shae';
        \$u = getenv('DB_USERNAME') ?: 'root';
        \$w = getenv('DB_PASSWORD') ?: '';
        new PDO(\"mysql:host=\$h;port=\$p;dbname=\$d\", \$u, \$w);
    " 2>/dev/null; then
        echo "[render] Database connected (attempt ${i})"
        db_ready=1
        break
    fi
    sleep 2
done

if [ "${db_ready}" -ne 1 ]; then
    echo "[render] ERROR: could not connect to MySQL"
    exit 1
fi

php artisan optimize:clear

echo "[render] Running migrations..."
php artisan migrate --force

echo "[render] Seeding if empty..."
php docker/seed-if-empty.php

php artisan storage:link 2>/dev/null || true

PORT="${PORT:-10000}"
echo "[render] Laravel listening on 0.0.0.0:${PORT}"
exec php artisan serve --host=0.0.0.0 --port="${PORT}"
