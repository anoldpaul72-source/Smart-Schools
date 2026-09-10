#!/bin/bash
set -e

# Write explicit, production-ready .env for Neon PostgreSQL
cat << 'EOF' > /var/www/html/.env
APP_NAME=Smart-Results
APP_ENV=production
APP_DEBUG=false
APP_URL=https://smart-schools-jr9n.onrender.com
DB_CONNECTION=pgsql
DB_HOST=ep-quiet-leaf-aykwszp4-pooler.c-5.us-east-2.aws.neon.tech
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=neondb_owner
DB_PASSWORD=npg_SfYcHR25DQqz
DB_SSLMODE="require;options='endpoint=ep-quiet-leaf-aykwszp4'"
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
EOF

# Preserve or generate APP_KEY
if [ -n "$APP_KEY" ]; then
    echo "APP_KEY=$APP_KEY" >> /var/www/html/.env
else
    php artisan key:generate --force
fi

# Pass DB environment variables directly into Apache envvars so mod_php receives them
echo "export DB_CONNECTION=pgsql" >> /etc/apache2/envvars
echo "export DB_HOST=ep-quiet-leaf-aykwszp4-pooler.c-5.us-east-2.aws.neon.tech" >> /etc/apache2/envvars
echo "export DB_PORT=5432" >> /etc/apache2/envvars
echo "export DB_DATABASE=neondb" >> /etc/apache2/envvars
echo "export DB_USERNAME=neondb_owner" >> /etc/apache2/envvars
echo "export DB_PASSWORD=npg_SfYcHR25DQqz" >> /etc/apache2/envvars
echo "export DB_SSLMODE=\"require;options='endpoint=ep-quiet-leaf-aykwszp4'\"" >> /etc/apache2/envvars

# Support Render custom $PORT
if [ -n "$PORT" ]; then
    sed -i "s/80/$PORT/g" /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf
fi

# Ensure storage directories exist with write permissions
mkdir -p /var/www/html/database \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

# Remove any existing SQLite database file to guarantee no fallback
rm -f /var/www/html/database/database.sqlite

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database /var/www/html/.env
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod 664 /var/www/html/.env

# Run database migrations and seeders on Neon
php artisan migrate --force
php artisan db:seed --force

# Clear caches for production
php artisan config:clear
php artisan route:clear
php artisan view:clear

chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database

echo "Smart-Schools is ready and permanently running on Neon PostgreSQL!"
exec apache2-foreground
