<?php

namespace Furniture\SkuOptionBundle\Twig;

use Furniture\ProductBundle\ProductRemoval\SkuOptionTypeRemovalChecker;
use Furniture\SkuOptionBundle\Entity\SkuOptionType;

/**
 * Class SkuOptionExtension
 * @package Furniture\SkuOptionBundle\Twig
 */
class SkuOptionExtension extends \Twig_Extension
{
    /**
     * @var SkuOptionTypeRemovalChecker
     */
    private $skuOptionRemovalChecker;

    /**
     * SkuOptionExtension constructor.
     *
     * @param SkuOptionTypeRemovalChecker $skuOptionRemovalChecker
     */
    public function __construct(SkuOptionTypeRemovalChecker $skuOptionRemovalChecker)
    {
        $this->skuOptionRemovalChecker = $skuOptionRemovalChecker;
    }


    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            'is_sku_option_can_remove' => new \Twig_Function_Method($this, 'isSkuOptionCanRemove'),
        ];
    }

    /**
     * Is sku option can remove?
     *
     * @param SkuOptionType $skuOptionType
     *
     * @return \Furniture\ProductBundle\ProductRemoval\SkuOptionTypeRemoval
     */
    public function isSkuOptionCanRemove(SkuOptionType $skuOptionType)
    {
        return $this->skuOptionRemovalChecker->canRemove($skuOptionType)->canRemove();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return 'sku_option';
    }
}
