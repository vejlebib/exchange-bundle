<?php
/**
 * Contains configuration loading.
 */

namespace Itk\ExchangeBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Os2Display\CoreBundle\DependencyInjection\Os2DisplayBaseExtension;

/**
 * This is the class that loads and manages your bundle configuration.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class ItkExchangeExtension extends Os2DisplayBaseExtension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $this->dir = __DIR__;

        parent::load($configs, $container);

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $def = $container->getDefinition('itk.exchange_service');
        $def->replaceArgument(3, $config['enabled']);
        $def->replaceArgument(4, $config['cache_ttl']);

        $def = $container->getDefinition('itk.exchange_soap_client');
        $def->replaceArgument(0, $config['host']);
        $def->replaceArgument(1, $config['user']);
        $def->replaceArgument(2, $config['password']);
        $def->replaceArgument(3, $config['version']);
    }
}
