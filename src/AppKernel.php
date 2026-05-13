<?php

use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernel extends Kernel
{
    public function registerBundles(): iterable
    {
        $contents = require $this->getProjectDir().'/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config_'.$this->environment.'.yml');
    }

    public function getCacheDir(): string
    {
        return __DIR__.'/../var/cache/'.$this->environment;
    }

    public function getLogDir(): string
    {
        return __DIR__.'/../logs';
    }

    /**
     * @return string
     */
    public function getProjectDir(): string
    {
        return __DIR__.'/../';
    }
}
