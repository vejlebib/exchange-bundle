<?php
/**
 * Contains configuration.
 */

namespace Itk\ExchangeBundle\DependencyInjection;

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
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('itk_exchange');

        // Set up re
        $rootNode
            ->children()
                ->booleanNode('enabled')->defaultFalse()->end()
                ->scalarNode('host')->isRequired()->end()
                ->scalarNode('user')->isRequired()->end()
                ->scalarNode('password')->isRequired()->end()
                ->scalarNode('version')->defaultValue('Exchange2010')->end()
                ->integerNode('cache_ttl')->defaultValue(1800)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
