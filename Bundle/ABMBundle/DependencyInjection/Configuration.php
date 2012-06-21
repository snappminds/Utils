<?php

namespace Snappminds\Utils\Bundle\ABMBundle\DependencyInjection;

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
        $rootNode = $treeBuilder->root('snappminds_utils_abm');
        $rootNode
            ->children()
                ->arrayNode('grid')
                    ->addDefaultsIfNotSet()
                    ->children()
                        ->scalarNode('rows_per_page')->defaultValue('10')->cannotBeEmpty()->end()
                    ->end()
                ->end()             
            ->end();

        return $treeBuilder;
    }
}
