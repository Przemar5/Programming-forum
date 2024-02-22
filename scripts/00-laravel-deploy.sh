#!/usr/bin/env bash
echo "Running composer"
composer global require hirak/prestissimo
# cd /var/www/html
# composer clear-cache
# cat composer.json
# chmod a+w -R vendor
# echo "Diagnose Composer"
# composer diagnose
# composer update symfonycasts/reset-password-bundle
# echo "composer require -vvv hirak/prestissimo"
# php -d memory_limit=-1 /usr/bin/composer require --no-cache -vvv --no-interaction hirak/prestissimo
# ls -l vendor
# rm -r vendor
# echo "Memory:"
# php -r "echo ini_get('memory_limit').PHP_EOL;"
# echo "Error handler:"
# php -d memory_limit=-1 /usr/bin/composer require vanilla/nbbc --no-cache --no-scripts --no-interaction -vvv --working-dir=/var/www/html
# echo "Test:"
# php -d memory_limit=-1 /usr/bin/composer require chriskonnertz/bbcode --no-scripts --no-interaction -vvv --working-dir=/var/www/html
echo "All:"
# composer install --help
composer install --no-dev --no-plugins --profile --no-scripts --no-interaction -vvv --working-dir=/var/www/html
# echo "Which composer"
# which composer
# chmod a+w -R vendor
# composer dump-autoload

# chmod a+w -R vendor
# ls -l
ls -l vendor/

echo "Running migrations..."
# php bin/console do:mi:mi
# php bin/console do:fi:lo

# bash