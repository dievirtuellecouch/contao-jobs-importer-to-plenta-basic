<?php

namespace DVC\JobsImporterToPlentaBasic\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
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
                ->integerNode('override_date_posted_threshold')
                    ->defaultValue(null)
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
