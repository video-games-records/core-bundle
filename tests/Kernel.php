<?php

namespace VideoGamesRecords\CoreBundle\Tests;

use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class Kernel extends BaseKernel
{
    use MicroKernelTrait {
        configureContainer as private configureContainerFromTrait;
        configureRoutes as private privateConfigureRoutesFromTrait;
    }

    /**
     * {@inheritDoc}
     * @see \Symfony\Component\HttpKernel\KernelInterface::registerBundles()
     */
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new VideoGamesRecordsCoreBundle(),
        ];
    }

    private function configureContainer(ContainerConfigurator $container, LoaderInterface $loader, ContainerBuilder $builder)
    {
        $loader->load($this->getProjectDir().'/tests/config.yaml');
    }
}