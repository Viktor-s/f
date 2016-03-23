<?php
/**
 * Created by Shtompel Konstantin.
 * User: synthetic
 * Date: 3/23/2016
 * Time: 11:38 AM
 */

namespace Furniture\PostgresSearchBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $tree = new TreeBuilder();
        $tree->root('furniture_postgres_search')
            ->end();

        return $tree;
    }
}
