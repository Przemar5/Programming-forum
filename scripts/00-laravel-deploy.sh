#!/usr/bin/env bash
echo "Running composer"
# composer global require hirak/prestissimo
composer install --no-dev --working-dir=/var/www/html

echo "Running migrations..."
php bin/console do:mi:mi
php bin/console do:fi:lo