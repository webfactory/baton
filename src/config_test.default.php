<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->extension('framework', [
        'test' => true,
        'session' => ['storage_factory_id' => 'session.storage.factory.mock_file'],
    ]);

    $container->extension('monolog', [
        'handlers' => [
            'main' => [
                // Disable logging during test execution to avoid file creation and console output.
                'type' => 'null',
                'priority' => -1,
            ],
        ],
    ]);
};
