#!/usr/bin/env bash
echo "Running composer"
composer global require hirak/prestissimo
cd /var/www/html
composer install

echo "Running migrations..."
php bin/console do:mi:mi
php bin/console do:fi:lo