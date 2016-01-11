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
use Sylius\Bundle\FixturesBundle\SyliusFixturesBundle;
use HWI\Bundle\OAuthBundle\HWIOAuthBundle;

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
            new Furniture\WebBundle\WebBundle(),
            new Furniture\SearchBundle\SearchBundle(),
            new Furniture\ProductBundle\ProductBundle(),
            new Furniture\VariationBundle\VariationBundle(),
            new Furniture\CommonBundle\CommonBundle(),
            new Furniture\SkuOptionBundle\SkuOptionBundle(),
            new Furniture\CompositionBundle\CompositionBundle(),
            new Furniture\FactoryBundle\FurnitureFactoryBundle(),
            new Furniture\RetailerBundle\RetailerBundle(),
            new Furniture\FrontendBundle\FrontendBundle(),
            new Furniture\SpecificationBundle\SpecificationBundle(),
            new Furniture\PostBundle\PostBundle(),
            new Furniture\UserBundle\UserBundle(),

            new Furniture\FixturesBundle\FixturesBundle(),

            new Knp\Bundle\MarkdownBundle\KnpMarkdownBundle(),
            new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new Sonata\FormatterBundle\SonataFormatterBundle(),
            new Furniture\PricingBundle\PricingBundle(),
            new Furniture\GoogleServicesBundle\GoogleServicesBundle(),
        );

        if (in_array($this->environment, array('dev', 'test'))) {
            $bundles[] = new \Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new \Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        $bundles = array_merge(parent::registerBundles(), $bundles);

        $bundles = array_filter($bundles, function ($bundle) {
            if ($bundle instanceof SyliusFixturesBundle) {
                return false;
            }

            return true;
        });

        return $bundles;
    }
}
