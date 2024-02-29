<?php

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\HttpFoundation\Request;

require dirname(__DIR__).'/vendor/autoload.php';


try {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env', $_SERVER['APP_ENV'] ?? 'dev');
} catch (\Exception $e) {}

try {
// dd(123);
if ($_SERVER['APP_DEBUG']) {
    umask(0000);

    Debug::enable();
}
// dd(12345678);
dump(1);
if ($trustedProxies = $_SERVER['TRUSTED_PROXIES'] ?? false) {
    Request::setTrustedProxies(explode(',', $trustedProxies), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}
dump(2);
if ($trustedHosts = $_SERVER['TRUSTED_HOSTS'] ?? false) {
    Request::setTrustedHosts([$trustedHosts]);
}
dump(3);
$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);
$request = Request::createFromGlobals();
dump(4);
$response = $kernel->handle($request);
dump(5);
// $response->send();
dump(6);
// $kernel->terminate($request, $response);
} catch (\Exception $e) {
    dd($e);
}