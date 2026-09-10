#!/bin/bash

echo "Starting Smart-Schools container boot..."

# 1. Write production .env file configured for Neon PostgreSQL
cat << 'EOF' > /var/www/html/.env
APP_NAME=Smart-Results
APP_ENV=production
APP_KEY=base64:VDH2jZq0vGMqHrU9AE/zwUOwugHgOzthSN66ftRRtJw=
APP_DEBUG=false
APP_URL=https://smart-schools-jr9n.onrender.com
DB_CONNECTION=pgsql
DB_HOST=ep-quiet-leaf-aykwszp4-pooler.c-5.us-east-2.aws.neon.tech
DB_PORT=5432
DB_DATABASE=neondb
DB_USERNAME=neondb_owner
DB_PASSWORD=npg_SfYcHR25DQqz
DB_SSLMODE=require
SESSION_DRIVER=file
CACHE_STORE=file
QUEUE_CONNECTION=sync
EOF

# 2. Support Render custom $PORT
if [ -n "$PORT" ]; then
    sed -i "s/80/$PORT/g" /etc/apache2/sites-available/*.conf /etc/apache2/ports.conf 2>/dev/null || true
fi

# 3. Ensure storage and bootstrap directories exist
mkdir -p /var/www/html/database \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/cache \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache

# Remove any SQLite database file so the app strictly connects to Neon
rm -f /var/www/html/database/database.sqlite

# 4. Set directory permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database /var/www/html/.env
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache /var/www/html/database
chmod 664 /var/www/html/.env

# 5. Run database migrations on Neon (non-blocking)
php artisan migrate --force || true

# 6. Clear and refresh caches
php artisan config:clear || true
php artisan route:clear || true
php artisan view:clear || true

# 7. Finalize permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

echo "Smart-Schools is ready and permanently running on Neon PostgreSQL!"
exec apache2-foreground
