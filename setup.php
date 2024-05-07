<?php

file_put_contents('.env', file_get_contents('.env.example'));

$fileContents = file_get_contents('docker-compose.yaml.dist');

file_put_contents('docker-compose.yaml', str_replace('$pathToApp', __DIR__, $fileContents));

echo exec('docker-compose up -d --build');
echo 'Containers should be up...' . PHP_EOL;

exec('docker-compose exec programming-forum-php composer install');
echo 'Composer dependencies downloaded...' . PHP_EOL;

echo "Everything's done" . PHP_EOL;