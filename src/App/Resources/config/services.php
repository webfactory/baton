<?php

declare(strict_types=1);

use Monolog\Formatter\LineFormatter;
use Monolog\Processor\WebProcessor;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $services = $container->services();

    $services->defaults()
        ->autowire()
        ->autoconfigure();

    $services->load('App\\', '../../')
        ->exclude([
            '../../Tests/',
            '../../Entity/',
            '../../Kernel.php',
            '../../Resources/',
        ]);

    $services->set('app.logging.web_processor', WebProcessor::class)
        ->args([null, []])
        ->tag('monolog.processor');

    $services->set('app.logging.line_formatter', LineFormatter::class)
        ->args(['%app.logging.default_line_format%', '%app.logging.default_time_format%'])
        ->call('includeStacktraces', [true])
        ->call('allowInlineLineBreaks', ['%app.logging.allow_inline_line_breaks%']);
};
