#!/bin/sh
set -e

cd /var/www

if [ ! -f ".env" ]; then
    cp .env.example .env
fi

if grep -q "^APP_KEY=$" .env; then
    php artisan key:generate --no-interaction --force
fi

echo "Waiting for database..."
until php -r "new PDO('mysql:host=${DB_HOST};port=${DB_PORT};dbname=${DB_DATABASE}', '${DB_USERNAME}', '${DB_PASSWORD}');" 2>/dev/null; do
    sleep 2
done

php artisan migrate --force --no-interaction

chmod -R 775 storage bootstrap/cache

php artisan config:cache
php artisan route:cache

exec "$@"
