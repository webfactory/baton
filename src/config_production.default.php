<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->parameters()
        ->set('app.logging.default_line_format', "[%%datetime%%] [%%extra.unique_id%%] %%channel%%.%%level_name%%: %%message%% [context: %%context%%] [extra: %%extra%%]\n")
        ->set('app.logging.allow_inline_line_breaks', false);

    // following https://github.com/symfony/recipes/blob/4fcfbbebe97e900ba47f94966a8ea4ab1080612d/doctrine/doctrine-bundle/1.12/config/packages/prod/doctrine.yaml
    $container->extension('doctrine', [
        'orm' => [
            'metadata_cache_driver' => ['type' => 'pool', 'pool' => 'doctrine.system_cache_pool'],
            'result_cache_driver' => ['type' => 'pool', 'pool' => 'doctrine.result_cache_pool'],
            'query_cache_driver' => ['type' => 'pool', 'pool' => 'doctrine.system_cache_pool'],
        ],
    ]);

    $container->extension('framework', [
        'cache' => [
            'pools' => [
                'doctrine.result_cache_pool' => ['adapter' => 'cache.app'],
                'doctrine.system_cache_pool' => ['adapter' => 'cache.system'],
            ],
        ],
    ]);

    $container->extension('monolog', [
        'handlers' => [
            'main' => [
                'type' => 'fingers_crossed',
                'passthru_level' => 'notice',
                'action_level' => 'warning',
                'handler' => 'nested',
                'priority' => -1,
            ],
            'nested' => [
                'type' => 'stream',
                'path' => '%kernel.logs_dir%/symfony.log',
                'level' => 'info',
                'formatter' => 'app.logging.line_formatter',
                'priority' => -1,
            ],
        ],
    ]);
};
