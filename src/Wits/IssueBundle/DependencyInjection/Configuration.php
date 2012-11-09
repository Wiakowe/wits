<?php

namespace Wits\IssueBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritDoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('wits_issue');

        $rootNode
            ->addDefaultsIfNotSet()
            ->children()
                ->arrayNode('allowed_hosts')
                    ->requiresAtLeastOneElement()
                    ->isRequired()
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('mailing_host')->isRequired()->cannotBeEmpty()->end()
                ->scalarNode('mailing_link_debug')->defaultValue('%kernel.debug%')->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
