<?php

namespace DVC\JobsImporterToPlentaBasic\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder('jobs_importer_to_plenta_basic');

        $treeBuilder->getRootNode()
            ->children()
                ->arrayNode('sources')
                    ->arrayPrototype()
                        ->children()
                            ->enumNode('type')
                                ->values(['talentstorm'])
                            ->end()
                            ->scalarNode('api_key')
                                ->defaultNull()
                            ->end()
                            ->integerNode('timeout')
                                ->defaultValue(3)
                            ->end()
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('mapping')
                    ->children()
                        ->arrayNode('organization')
                            ->arrayPrototype()
                                ->children()
                                    ->scalarNode('label')->end()
                                    ->integerNode('id')->end()
                                ->end()
                            ->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
