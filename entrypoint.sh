#!/bin/bash
set -e

# Support Render custom $PORT
if [ -n "$PORT" ]; then
    sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf
fi

# Ensure sqlite database exists and permissions are writable
touch /var/www/html/database/database.sqlite
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

# Run database migrations and seeders
php artisan migrate --force
php artisan db:seed --force

# Optimize Laravel for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Smart-Schools is ready and running on Render!"
exec apache2-foreground
