#!/bin/bash
set -e

# If .env does not exist, create one from .env.example
if [ ! -f /var/www/html/.env ]; then
    cp /var/www/html/.env.example /var/www/html/.env || touch /var/www/html/.env
fi

# Ensure essential env configurations for Render
export APP_ENV=${APP_ENV:-production}
export APP_DEBUG=${APP_DEBUG:-false}
export DB_CONNECTION=sqlite
export DB_DATABASE=/var/www/html/database/database.sqlite
export SESSION_DRIVER=file
export CACHE_STORE=file
export QUEUE_CONNECTION=sync

# Update .env file settings if needed
sed -i 's/^DB_CONNECTION=.*/DB_CONNECTION=sqlite/' /var/www/html/.env || echo "DB_CONNECTION=sqlite" >> /var/www/html/.env
sed -i 's/^SESSION_DRIVER=.*/SESSION_DRIVER=file/' /var/www/html/.env || echo "SESSION_DRIVER=file" >> /var/www/html/.env
sed -i 's/^CACHE_STORE=.*/CACHE_STORE=file/' /var/www/html/.env || echo "CACHE_STORE=file" >> /var/www/html/.env

# Generate APP_KEY if empty in .env or not set
if ! grep -q "^APP_KEY=base64:" /var/www/html/.env; then
    php artisan key:generate --force
fi

# Support Render custom $PORT
if [ -n "$PORT" ]; then
    sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf
fi

# Ensure directories and database file exist with write permissions
mkdir -p /var/www/html/database \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

touch /var/www/html/database/database.sqlite

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database /var/www/html/.env
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod 664 /var/www/html/.env

# Run database migrations and seeders
php artisan migrate --force
php artisan db:seed --force

# Clear and optimize Laravel for production
php artisan config:clear
php artisan route:clear
php artisan view:clear

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

echo "Smart-Schools is ready and running on Render!"
exec apache2-foreground
