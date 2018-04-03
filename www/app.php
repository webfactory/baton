<?php

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Debug\Debug;

require __DIR__.'/../vendor/autoload.php';

Request::enableHttpMethodParameterOverride();
Request::setTrustedHeaderName(Request::HEADER_FORWARDED, null);
$request = Request::createFromGlobals();

$env = getenv('APP_ENV') ?: 'development';
$debug = getenv('APP_DEBUG') != '0';
$enableCache = (bool)getenv('SYMFONY_WEBAPP_CACHE_ENABLED');

if (getenv('SYMFONY_WEBAPP_KERNEL_PARAMETERS_ALLOW_OVERRIDE')) {
    if ($request->cookies->has('SYMFONY_ENV')) {
        $env = $request->cookies->get('SYMFONY_ENV');
    }
    if ($request->cookies->has('SYMFONY_NODEBUG')) {
        $debug = false;
    }
    if ($request->cookies->has('SYMFONY_CACHE')) {
        $enableCache = true;
    }
}

if ($debug) {
    Debug::enable(error_reporting());
}

$kernel = new AppKernel($env, $debug);

if (!$kernel->isDebug()) {
    $kernel->loadClassCache();
}

if ($enableCache) {
    class AppCache extends \Symfony\Bundle\FrameworkBundle\HttpCache\HttpCache
    {
    }
    $kernel = new AppCache($kernel);
}

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
