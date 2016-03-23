<?php

namespace Furniture\PricingBundle\Twig;

use Furniture\PricingBundle\Calculator\PriceCalculator;
use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Entity\ProductVariant;
use Furniture\SpecificationBundle\Entity\Specification;
use Furniture\SpecificationBundle\Entity\SpecificationItem;
use Sylius\Bundle\CurrencyBundle\Templating\Helper\MoneyHelper;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Furniture\ProductBundle\Pattern\ProductVariantPriceCompiller;
use Furniture\ProductBundle\Entity\ProductVariantsPattern;

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
     *
     * @var \Furniture\ProductBundle\Pattern\ProductVariantPriceCompiller
     */
    protected $productPatternVariantPriceCompiller;
    
    /**
     * Construct
     *
     * @param PriceCalculator          $calculator
     * @param CurrencyContextInterface $currencyContext
     * @param MoneyHelper              $moneyHelper
     * @param \Furniture\ProductBundle\Pattern\ProductVariantPriceCompiller $productPatternVariantPriceCompiller
     */
    public function __construct(
        PriceCalculator $calculator,
        CurrencyContextInterface $currencyContext,
        MoneyHelper $moneyHelper,
        ProductVariantPriceCompiller $productPatternVariantPriceCompiller
    )
    {
        $this->calculator = $calculator;
        $this->currencyContext = $currencyContext;
        $this->moneyHelper = $moneyHelper;
        $this->productPatternVariantPriceCompiller = $productPatternVariantPriceCompiller;
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
            'money'                          => new \Twig_Filter_Method($this, 'money'),
            'pattern_price'              => new \Twig_Filter_Method($this, 'patternPrice'),
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
        return $this->calculator->calculateTotalForSpecificationItem($specificationItem);
    }

    /**
     * Get specification total price
     *
     * @param Specification $specification
     * @param bool          $useSales
     *
     * @return int
     */
    public function specificationTotalPrice(Specification $specification, $useSales = true)
    {
        return $this->calculator->calculateForSpecification($specification, $useSales);
    }

    public function patternPrice(ProductVariantsPattern $pattern){
        return $this->calculator->calculateForPattern($pattern);
    }


    /**
     * Format money
     *
     * @param int  $amount
     * @param bool $precision
     *
     * @return string
     */
    public function money($amount, $precision = false)
    {
        $currency = $this->currencyContext->getCurrency();
        $amount = str_replace(' ', '', $this->moneyHelper->formatAmount($amount, $currency));
        $suffix = preg_replace('/[0-9\.]+/', '', $amount);

        if (false !== $precision) {
            $amount = (0 === $precision) ? ceil((float)$amount) : round((float)$amount, $precision);
            $amount = number_format($amount, $precision, '.', ' ');
        } else {
            $amount = substr($amount, 0, -strlen($suffix));
            $amount = trim(number_format($amount, 2, '.', ' '), '0');
        }

        $amount = sprintf('%s %s', $amount, $suffix);

        return $amount;
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'pricing_extension';
    }
}
