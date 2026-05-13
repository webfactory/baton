<?php

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->import(__DIR__.'/App/Controller/', 'attribute')
        ->prefix('/')
        ->defaults(['_locale' => 'en']);
};