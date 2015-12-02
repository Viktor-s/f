<?php

namespace Furniture\GoogleServicesBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Furniture\GoogleServicesBundle\DependencyInjection\Configuration;

class GoogleServicesExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $config, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $configuration = new Configuration();
        $processedConfig = $this->processConfiguration( $configuration, $config );
        
        $container->setParameter( 'google_services.maps_access_key', $processedConfig['maps_access_key']);
    }
}
