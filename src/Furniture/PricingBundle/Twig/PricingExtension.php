<?php

namespace Furniture\PricingBundle\Twig;

use Furniture\PricingBundle\Calculator\PriceCalculator;
use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Entity\ProductVariant;
use Furniture\SpecificationBundle\Entity\Specification;
use Furniture\SpecificationBundle\Entity\SpecificationItem;
use Sylius\Bundle\CurrencyBundle\Templating\Helper\MoneyHelper;
use Sylius\Component\Currency\Context\CurrencyContextInterface;

class PricingExtension extends \Twig_Extension
{
    /**
     * @var PriceCalculator
     */
    protected $calculator;

    /**
     * @var MoneyHelper
     */
    protected $moneyHelper;

    /**
     * @var CurrencyContextInterface
     */
    protected $currencyContext;

    /**
     * Construct
     *
     * @param PriceCalculator          $calculator
     * @param CurrencyContextInterface $currencyContext
     * @param MoneyHelper              $moneyHelper
     */
    public function __construct (
        PriceCalculator $calculator,
        CurrencyContextInterface $currencyContext,
        MoneyHelper $moneyHelper
    )
    {
        $this->calculator = $calculator;
        $this->currencyContext = $currencyContext;
        $this->moneyHelper = $moneyHelper;
    }

    /**
     * {@inheritDoc}
     */
    public function getFilters()
    {
        return [
            'product_price'                  => new \Twig_Filter_Method($this, 'productPrice'),
            'sku_price'                      => new \Twig_Filter_Method($this, 'skuPrice'),
            'specification_item_total_price' => new \Twig_Filter_Method($this, 'specificationItemTotalPrice'),
            'specification_total_price'      => new \Twig_Filter_Method($this, 'specificationTotalPrice'),
            'money'                          => new \Twig_Filter_Method($this, 'money')
        ];
    }

    /**
     * Get price for product
     *
     * @param Product $product
     *
     * @return int
     */
    public function productPrice(Product $product)
    {
        return $this->calculator->calculateForProduct($product);
    }

    /**
     * Get price for product variant
     *
     * @param ProductVariant $sku
     *
     * @return int
     */
    public function skuPrice(ProductVariant $sku)
    {
        return $this->calculator->calculateForProductVariant($sku);
    }

    /**
     * Get specification item total price
     *
     * @param SpecificationItem $specificationItem
     *
     * @return int
     */
    public function specificationItemTotalPrice(SpecificationItem $specificationItem)
    {
        return $this->calculator->calculateForSpecificationItem($specificationItem);
    }

    /**
     * Get specification total price
     *
     * @param Specification $specification
     *
     * @return int
     */
    public function specificationTotalPrice(Specification $specification)
    {
        return $this->calculator->calculateForSpecification($specification);
    }

    /**
     * Format money
     *
     * @param int $amount
     *
     * @return string
     */
    public function money($amount)
    {
        $currency = $this->currencyContext->getCurrency();

        return $this->moneyHelper->formatAmount($amount, $currency);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'pricing_extension';
    }
}
