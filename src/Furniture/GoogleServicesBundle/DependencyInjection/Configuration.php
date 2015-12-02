<?php
namespace Furniture\GoogleServicesBundle\DependencyInjection;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
    
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('google_services');

        $rootNode
        ->children()
            ->scalarNode('maps_access_key')
            ->end()
        ->end();

        return $treeBuilder;
    }
    
}


