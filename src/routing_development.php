<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $profilerDir = __DIR__.'/../vendor/symfony/web-profiler-bundle/Resources/config/routing';
    $routes->import($profilerDir.'/wdt.php')->prefix('/_wdt');
    $routes->import($profilerDir.'/profiler.php')->prefix('/_profiler');

    $routes->import(__DIR__.'/routing.php');
};
