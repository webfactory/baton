<?php

declare(strict_types=1);

use Monolog\Processor\PsrLogMessageProcessor;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->parameters()
        ->set('app.logging.allow_inline_line_breaks', true)
        ->set('app.logging.default_line_format', "[%%datetime%%] [%%extra.unique_id%%]\n\n\t%%channel%%.%%level_name%%: %%message%%\n\n\tcontext: %%context%%\n\n\textra: %%extra%%\n\n");

    $container->extension('framework', [
        'profiler' => ['enabled' => true],
    ]);

    $container->extension('web_profiler', [
        'toolbar' => true,
    ]);

    $container->extension('twig', [
        'cache' => false,
    ]);

    $container->extension('monolog', [
        'handlers' => [
            'main' => [
                'formatter' => 'app.logging.line_formatter',
                'type' => 'stream',
                'path' => '%kernel.logs_dir%/%kernel.environment%.log',
                'level' => 'debug',
                'channels' => ['!event'],
                'priority' => -1,
            ],
        ],
    ]);

    // In development, use PsrLogMessageProcessor to expand "{value}" placeholders in log
    // messages with corresponding values from the log record context.
    $container->services()
        ->set('app.logging.psr_log_message_processor', PsrLogMessageProcessor::class)
        ->tag('monolog.processor');
};
