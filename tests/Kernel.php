<?php

namespace VideoGamesRecords\CoreBundle\Tests;

use Aws\Symfony\AwsBundle;
use Doctrine\Bundle\DoctrineBundle\DoctrineBundle;
use Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle;
use Exception;
use League\FlysystemBundle\FlysystemBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\SecurityBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Bundle\WebProfilerBundle\WebProfilerBundle;
use VideoGamesRecords\CoreBundle\VideoGamesRecordsCoreBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class Kernel extends BaseKernel
{
    use MicroKernelTrait {
        MicroKernelTrait::configureContainer as private configureContainerFromTrait;
        MicroKernelTrait::configureRoutes as private privateConfigureRoutesFromTrait;
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
            new DoctrineBundle(),
            new SecurityBundle(),
            new FlysystemBundle(),
            new AwsBundle(),
            new WebProfilerBundle(),
            new DoctrineFixturesBundle(),
            new TwigBundle(),
        ];
    }

    /**
     * @param LoaderInterface $loader
     * @throws Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config/packages.yaml');
    }
}
