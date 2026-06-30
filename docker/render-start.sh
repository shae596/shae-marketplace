#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

echo "[render] Boot SHAE..."

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_DATABASE="${DB_DATABASE:-shae}"
DB_USERNAME="${DB_USERNAME:-root}"
DB_PASSWORD="${DB_PASSWORD:-}"

chmod -R 775 storage bootstrap/cache 2>/dev/null || true

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
EOSQL

    if [ "${DB_USERNAME}" != "root" ]; then
        mariadb -u root <<-EOSQL
CREATE USER IF NOT EXISTS '${DB_USERNAME}'@'127.0.0.1' IDENTIFIED BY '${DB_PASSWORD}';
CREATE USER IF NOT EXISTS '${DB_USERNAME}'@'localhost' IDENTIFIED BY '${DB_PASSWORD}';
GRANT ALL PRIVILEGES ON \`${DB_DATABASE}\`.* TO '${DB_USERNAME}'@'127.0.0.1';
GRANT ALL PRIVILEGES ON \`${DB_DATABASE}\`.* TO '${DB_USERNAME}'@'localhost';
FLUSH PRIVILEGES;
EOSQL
    fi
}

if [ "${DB_HOST}" = "127.0.0.1" ] || [ "${DB_HOST}" = "localhost" ]; then
    start_embedded_mysql
else
    echo "[render] External MySQL: ${DB_HOST}"
fi

echo "[render] Waiting for database connection..."
for i in $(seq 1 45); do
    if php -r "
        \$h = getenv('DB_HOST') ?: '127.0.0.1';
        \$p = getenv('DB_PORT') ?: '3306';
        \$d = getenv('DB_DATABASE') ?: 'shae';
        \$u = getenv('DB_USERNAME') ?: 'root';
        \$w = getenv('DB_PASSWORD') ?: '';
        new PDO(\"mysql:host=\$h;port=\$p;dbname=\$d\", \$u, \$w);
    " 2>/dev/null; then
        echo "[render] Database connected (${i})"
        break
    fi
    sleep 2
done

php artisan optimize:clear

echo "[render] Running migrations..."
php artisan migrate --force

echo "[render] Seeding if empty..."
php artisan tinker --execute="if (\\App\\Models\\User::query()->count() === 0) { \\Illuminate\\Support\\Facades\\Artisan::call('db:seed', ['--force' => true]); echo 'Seeded'; } else { echo 'Skip seed'; }"

php artisan storage:link 2>/dev/null || true

php artisan config:cache
php artisan route:cache
php artisan view:cache

PORT="${PORT:-10000}"
echo "[render] Laravel listening on 0.0.0.0:${PORT}"
exec php artisan serve --host=0.0.0.0 --port="${PORT}"
