#!/usr/bin/env bash
echo "Running composer"
composer global require hirak/prestissimo
cd /var/www/html
composer clear-cache
cat composer.json
chmod a+w -R vendor
# composer update symfonycasts/reset-password-bundle
echo "uname -v"
uname -v
composer require hirak/prestissimo
ls -l vendor
composer install --no-dev -vvv --working-dir=/var/www/html
chmod a+w -R vendor
composer dump-autoload
chmod a+w -R vendor
ls -l
ls -l vendor/

echo "Running migrations..."
php bin/console do:mi:mi
php bin/console do:fi:lo