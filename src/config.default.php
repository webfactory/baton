<?php

declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->parameters()
        ->set('env(MYSQL_HOST)', 'mysql')
        ->set('env(MYSQL_USER)', 'baton')
        ->set('env(MYSQL_PASSWORD)', 'baton')
        ->set('env(MYSQL_DATABASE)', 'baton')
        ->set('env(MYSQL_PORT)', '3306')
        ->set('mysql.host', '%env(MYSQL_HOST)%')
        ->set('mysql.user', '%env(MYSQL_USER)%')
        ->set('mysql.password', '%env(MYSQL_PASSWORD)%')
        ->set('mysql.db', '%env(MYSQL_DATABASE)%')
        ->set('mysql.port', '%env(MYSQL_PORT)%')
        ->set('framework.trusted_hosts', ['127.0.0.1', 'localhost', '%env(HOSTNAME)%'])
        ->set('app.logging.default_time_format', 'Y-m-d H:i:s.u T')
        ->set('app.logging.allow_inline_line_breaks', false)
        ->set('demo_mode', '%env(DEMO_MODE)%')
        ->set('env(DEMO_MODE)', null)
        ->set('secret', '%env(SECRET)%')
        ->set('env(SECRET)', 'change_me')
        ->set('app.github.token', '%env(GITHUB_OAUTH_TOKEN)%')
        ->set('app.github.webhook_secret', '%env(GITHUB_WEBHOOK_SECRET)%')
        ->set('env(GITHUB_OAUTH_TOKEN)', null)
        ->set('env(GITHUB_WEBHOOK_SECRET)', null)
        ->set('env(HOSTNAME)', 'localhost');

    $container->extension('framework', [
        'secret' => '%secret%',
        'form' => ['csrf_protection' => ['enabled' => false]],
        'default_locale' => 'de_DE',
        'trusted_hosts' => '%framework.trusted_hosts%',
    ]);

    $container->extension('twig', [
        'strict_variables' => '%kernel.debug%',
        'form_themes' => ['Form/fields.html.twig'],
    ]);

    $container->extension('doctrine', [
        'dbal' => [
            'host' => '%mysql.host%',
            'port' => '%mysql.port%',
            'dbname' => '%mysql.db%',
            'user' => '%mysql.user%',
            'password' => '%mysql.password%',
            'charset' => 'utf8',
            'schema_filter' => '~^(?!(_dbversion$|_fixed_tableid$|wfd_))~',
            'server_version' => '5.7',
        ],
        'orm' => [
            'enable_native_lazy_objects' => true,
            'mappings' => [
                'App' => [
                    'is_bundle' => false,
                    'type' => 'attribute',
                    'dir' => '%kernel.project_dir%/src/App/Entity',
                    'prefix' => 'App\\Entity',
                    'alias' => 'App',
                ],
            ],
        ],
    ]);
};
