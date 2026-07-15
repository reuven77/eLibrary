#!/bin/sh
set -e

APP_HOME="$(CDPATH= cd -- "$(dirname "$0")/.." && pwd)"
cd "$APP_HOME"

# Railway injects PORT as a string; cast to int for php -S / artisan compatibility.
PORT="$(printf '%d' "${PORT:-8000}" 2>/dev/null || echo 8000)"

test -f "$APP_HOME/public/index.php"
test -f "$APP_HOME/server.php"

php artisan storage:link --force
php artisan migrate --force
# Railway Postgres ≠ DB lokal; isi demo hanya jika masih kosong
php artisan ruangbaca:seed-if-empty
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "RuangBaca: listening on 0.0.0.0:${PORT} via ${APP_HOME}/server.php"

# -t public: static assets. server.php: routes (uses __DIR__/public, not getcwd).
exec php -S "0.0.0.0:${PORT}" -t "$APP_HOME/public" "$APP_HOME/server.php"
