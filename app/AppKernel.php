<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Sylius\Bundle\CoreBundle\Kernel\Kernel;

/**
 * Sylius application kernel.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AppKernel extends Kernel
{
    /**
     * {@inheritdoc}
     */
    public function registerBundles()
    {
        $bundles = array(
            new \Furniture\WebBundle\WebBundle(),
            new \Furniture\SearchBundle\SearchBundle(),
            new \Furniture\ProductBundle\ProductBundle(),
            new \Furniture\VariationBundle\VariationBundle(),
            new \Furniture\CommonBundle\CommonBundle(),
            new \Furniture\SkuOptionBundle\SkuOptionBundle(),
            new \Furniture\FactoryBundle\FurnitureFactoryBundle(),
            new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
            new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new Sonata\FormatterBundle\SonataFormatterBundle(),
        );

        if (in_array($this->environment, array('dev', 'test'))) {
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new \Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return array_merge(parent::registerBundles(), $bundles);
    }
}
