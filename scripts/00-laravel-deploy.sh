#!/usr/bin/env bash
echo "Running composer"
composer global require hirak/prestissimo
cd /var/www/html
composer clear-cache
composer diagnose
# composer reinstall
composer install --no-dev -vvv --working-dir=/var/www/html

echo "Running migrations..."
php bin/console do:mi:mi
php bin/console do:fi:lo