<?php

namespace Furniture\CompositionBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

class CompositionExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
    }

    /**
     * {@inheritDoc}
     */
    public function getAlias()
    {
        return 'furniture_composition';
    }
}
