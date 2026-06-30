#!/usr/bin/env bash
set -euo pipefail

cd /var/www/html

DB_HOST="${DB_HOST:-127.0.0.1}"
DB_DATABASE="${DB_DATABASE:-shae}"
DB_USERNAME="${DB_USERNAME:-shae}"
DB_PASSWORD="${DB_PASSWORD:-shae}"

start_embedded_mysql() {
    echo "[render] Starting MariaDB (MySQL)..."
    if [ ! -f /var/lib/mysql/.shae-initialized ]; then
        mariadb-install-db --user=mysql --datadir=/var/lib/mysql --auth-root-authentication-method=normal
        touch /var/lib/mysql/.shae-initialized
    fi

    mysqld_safe --datadir=/var/lib/mysql --bind-address=127.0.0.1 &
    for _ in $(seq 1 45); do
        if mariadb-admin ping --silent 2>/dev/null; then
            break
        fi
        sleep 1
    done

    mariadb -u root <<-EOSQL
		CREATE DATABASE IF NOT EXISTS \`${DB_DATABASE}\`;
		CREATE USER IF NOT EXISTS '${DB_USERNAME}'@'127.0.0.1' IDENTIFIED BY '${DB_PASSWORD}';
		GRANT ALL PRIVILEGES ON \`${DB_DATABASE}\`.* TO '${DB_USERNAME}'@'127.0.0.1';
		FLUSH PRIVILEGES;
	EOSQL
}

if [ "${DB_HOST}" = "127.0.0.1" ] || [ "${DB_HOST}" = "localhost" ]; then
    start_embedded_mysql
else
    echo "[render] Using external MySQL at ${DB_HOST}"
fi

echo "[render] Waiting for database..."
for _ in $(seq 1 30); do
    if php -r "
        \$h = getenv('DB_HOST') ?: '127.0.0.1';
        \$p = getenv('DB_PORT') ?: '3306';
        \$d = getenv('DB_DATABASE') ?: 'shae';
        \$u = getenv('DB_USERNAME') ?: 'shae';
        \$w = getenv('DB_PASSWORD') ?: '';
        new PDO(\"mysql:host=\$h;port=\$p;dbname=\$d\", \$u, \$w);
    " 2>/dev/null; then
        break
    fi
    sleep 2
done

echo "[render] Clearing config cache..."
php artisan config:clear

echo "[render] Running migrations..."
php artisan migrate --force

echo "[render] Seeding database if empty..."
php artisan tinker --execute="if (\\App\\Models\\User::query()->count() === 0) { \\Illuminate\\Support\\Facades\\Artisan::call('db:seed', ['--force' => true]); echo 'Seeded'; } else { echo 'Skip seed'; }"

echo "[render] Storage link..."
php artisan storage:link 2>/dev/null || true

echo "[render] Caching config/routes/views..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

PORT="${PORT:-10000}"
echo "[render] Starting Laravel on 0.0.0.0:${PORT}..."
exec php artisan serve --host=0.0.0.0 --port="${PORT}"
