<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->parameters()
        ->set('app.logging.default_line_format', "[%%datetime%%] [%%extra.unique_id%%] %%channel%%.%%level_name%%: %%message%% [context: %%context%%] [extra: %%extra%%]\n")
        ->set('app.logging.allow_inline_line_breaks', false);

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
