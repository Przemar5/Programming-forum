#!/usr/bin/env bash
echo "Running composer"
composer install --working-dir=/var/www/html
# composer require doctrine/doctrine-fixtures-bundle --dev

echo "Running migrations..."
php bin/console do:mi:mi