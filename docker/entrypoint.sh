#!/bin/sh
set -eu

cd /var/www/html

for required_variable in APP_KEY DB_PASSWORD SUPABASE_URL SUPABASE_SERVICE_ROLE_KEY; do
    if [ -z "$(printenv "$required_variable" || true)" ]; then
        echo "$required_variable must be set before StuDyIo can start." >&2
        exit 1
    fi
done

php artisan config:cache
php artisan migrate --force --seed
php artisan view:cache

exec "$@"
