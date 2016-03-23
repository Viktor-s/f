<?php
/**
 * Created by Shtompel Konstantin.
 * User: synthetic
 * Date: 3/23/2016
 * Time: 11:35 AM
 */

namespace Furniture\PostgresSearchBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

class PostgresSearchExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $config = [
            'dbal' => [
                'types'         => [
                    'tsvector' => 'Furniture\PostgresSearchBundle\DBAL\TsvectorType',
                ],
                'mapping_types' => [
                    'tsvector' => 'tsvector',
                ],
            ],
            'orm'  => [
                'dql' => [
                    'string_functions' => [
                        'tsquery'         => 'Furniture\PostgresSearchBundle\DQL\TsqueryFunction',
                        'plainto_tsquery' => 'Furniture\PostgresSearchBundle\DQL\PlainToTsqueryFunction',
                        'tsrank'          => 'Furniture\PostgresSearchBundle\DQL\TsrankFunction',
                        'tsheadline'      => 'Furniture\PostgresSearchBundle\DQL\TsheadlineFunction',
                    ],
                ],
            ],
        ];

        $container->prependExtensionConfig('doctrine', $config);
    }
}
