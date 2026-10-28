#!/bin/sh
set -eu

mkdir -p storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

if [ "${RUN_MIGRATIONS:-false}" = "true" ]; then
    gosu www-data php artisan migrate --force --no-interaction
fi

exec "$@"
