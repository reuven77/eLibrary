#!/bin/sh
set -e

# Railway injects PORT as a string; artisan serve does `$port + offset` and
# fatals on PHP 8+ when the value is non-numeric. Use php -S with an int port.
PORT="$(printf '%d' "${PORT:-8000}" 2>/dev/null || echo 8000)"

php artisan storage:link --force
php artisan migrate --force
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "RuangBaca: listening on 0.0.0.0:${PORT} (php -S, not artisan serve)"

# Laravel's server.php uses getcwd() as the public path (same as artisan serve).
cd public
exec php -S "0.0.0.0:${PORT}" \
    ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php
