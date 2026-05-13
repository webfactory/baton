<?php

declare(strict_types=1);

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container, LoaderInterface $loader, ContainerBuilder $builder): void
    {
        $loader->load(__DIR__.'/config_'.$this->environment.'.yml');
        $loader->load(__DIR__.'/App/Resources/config/services.yml');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        if ('development' === $this->environment) {
            $routes->import(__DIR__.'/routing_development.php');
        } else {
            $routes->import(__DIR__.'/routing.php');
        }
    }

    public function getLogDir(): string
    {
        return __DIR__.'/../logs';
    }
}
