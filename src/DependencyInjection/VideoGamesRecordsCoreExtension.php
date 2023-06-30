<?php

namespace VideoGamesRecords\CoreBundle\DependencyInjection;

use Exception;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * @link http://symfony.com/doc/current/cookbook/bundles/extension.html
 */
class VideoGamesRecordsCoreExtension extends Extension
{
    /**
     * @param array            $configs
     * @param ContainerBuilder $container
     * @throws Exception
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $this->processConfiguration($configuration, $configs);
        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('services/commands.yml');
        $loader->load('services/controllers.yml');
        $loader->load('services/data_providers.yml');
        $loader->load('services/doctrine_extension.yml');
        $loader->load('services/event_subscriber.yml');
        $loader->load('services/event_listener.yml');
        $loader->load('services/handlers.yml');
        $loader->load('services/managers.yml');
        $loader->load('services/providers.yml');
        $loader->load('services/ranking/commands.yml');
        $loader->load('services/ranking/providers.yml');
        $loader->load('services/repositories.yml');
        $loader->load('services/transformers.yml');
        $loader->load('admin.yml');
    }
}
