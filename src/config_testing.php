<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->import(__DIR__.'/config_production.php');
    $container->import(__DIR__.'/config_testing.default.php');
    $container->import(__DIR__.'/config_testing.local.yml', null, true);
};
