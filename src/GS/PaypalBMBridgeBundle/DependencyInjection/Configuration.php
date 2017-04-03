<?php

namespace GS\PaypalBMBridgeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files.
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/configuration.html}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('gs_paypal_bm_bridge');

        // Here you should define the parameters that are allowed to
        // configure your bundle. See the documentation linked above for
        // more information on that topic.

        $rootNode->children()
                ->scalarNode("environment")
                    ->defaultValue("sandbox")
                    ->validate()
                        ->ifNotInArray(array("sandbox", "production"))
                        ->thenInvalid("Enviroment must be either 'sandbox' or 'production'")
                    ->end()
                ->end()
                ->arrayNode("sandbox")
                    ->children()
                        ->scalarNode('username')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('password')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('signature')
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode("production")
                    ->children()
                        ->scalarNode('username')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('password')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('signature')
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('logs')
                    ->children()
                        ->scalarNode('enabled')
                            ->defaultValue(true)
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('filename')
                            ->defaultValue('%kernel.root_dir%/logs/paypal.log')
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('level')
                            ->defaultValue('ERROR')
                            ->validate()
                                ->ifNotInArray(array('FINE', 'INFO', 'ERROR', 'WARN'))
                                ->thenInvalid('Invalid log level type of "%s"')
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('http')
                    ->children()
                        ->scalarNode('timeout')
                            ->defaultValue(5000)
                            ->cannotBeEmpty()
                        ->end()
                        ->scalarNode('retry')
                            ->defaultValue(2)
                            ->cannotBeEmpty()
                        ->end()
                    ->end()
                ->end()
            ->end();
        
        return $treeBuilder;
    }
}
