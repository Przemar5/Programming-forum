#!/usr/bin/env bash
echo "Running composer"
composer global require hirak/prestissimo
cd /var/www/html
composer clear-cache
composer diagnose
# composer reinstall
cat composer.json
composer update symfonycasts/reset-password-bundle
composer install --no-dev -vvv --working-dir=/var/www/html
composer dump-autoload
# chmod o+w
ls -l

echo "Running migrations..."
php bin/console do:mi:mi
php bin/console do:fi:lo