<?php

namespace Wiakowe\FetchBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\Config\FileLocator;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class WiakoweFetchExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $this->createClients($config, $container);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('processor.xml');
    }

    protected function createClients(array $config, ContainerBuilder $container)
    {
        $clientPrefix = 'wiakowe_fetch.clients.';

        foreach ($config['clients'] as $clientName => $clientConfig) {
            $definition = new Definition('Fetch\Server', array($clientConfig['host'], $clientConfig['port']));
            $definition->addMethodCall('setAuthentication', array($clientConfig['user'], $clientConfig['password']));

            $container->setDefinition($clientPrefix . $clientName, $definition);
        }

        $container->setAlias('wiakowe_fetch.client', $clientPrefix . $config['default_client']);
    }
}
