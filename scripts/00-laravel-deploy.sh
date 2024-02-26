#!/usr/bin/env bash
echo "Running composer"
composer --version
# composer global require hirak/prestissimo
# cd /var/www/html
composer install --working-dir=/var/www/html

# ls -l
# ls -l vendor/

echo "Running migrations..."
php bin/console do:mi:mi
# php bin/console do:fi:lo

# bash