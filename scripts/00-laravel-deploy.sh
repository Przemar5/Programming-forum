#!/usr/bin/env bash
echo "Running composer"
composer install --working-dir=/var/www/html

echo "Running migrations..."
php bin/console do:mi:mi
php bin/console doctrine:fixtures:load