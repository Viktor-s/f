<?php
namespace Furniture\ProductBundle\Pattern;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\ProductBundle\Entity\ProductVariant;
use Furniture\ProductBundle\Entity\ProductVariantsPattern;
use Furniture\ProductBundle\Entity\ProductVariantsPatternModifier;
use Furniture\ProductBundle\Model\PatternModifiersPrice;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ProductVariantPriceCompiller {
    
    /**
     * @var EntityManagerInterface
     */
    private $em;
    
    /**
     * Construct
     *
     * @param ProductVariantFinder   $finder
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    
    public function getAllGlobalModifiers(ProductVariantsPattern $pattern){
        return $this->em->getRepository(ProductVariantsPatternModifier::class)->findBy( ['product' => $pattern->getProduct(), 'pattern' => null]);
    }

    public function getLocalModifiers(ProductVariantsPattern $pattern){
        
    }

    public function gteAvailablePrices(ProductVariantsPattern $pattern){
        
    }

    /**
     * if $parameters null - only global modifier will be apply
     * 
     * @param \Furniture\ProductBundle\Pattern\ProductVariantsPattern $pattern
     * @param \Furniture\ProductBundle\Pattern\ProductVariantParameters $parameters
     * @return int
     */
    public function getPrices(ProductVariantsPattern $pattern, ProductVariantParameters $parameters = null){
        return;
    }

    public function getMinPrice(ProductVariantsPattern $pattern) {
        $atternModifiersPrice = new PatternModifiersPrice($pattern, $this->getAllGlobalModifiers($pattern));
        $minPrice = false;
        foreach($atternModifiersPrice->getModifiers() as $modifier){
            $price = 0;
            foreach( $atternModifiersPrice->getCombinedModifiersWith($modifier) as $modifier ){
                $price += $this->applyModifierForPrice($modifier, $pattern->getPrice());
            }
            if( !$minPrice || $minPrice > $price ){
                $minPrice = $price;
            }
        }
        return $minPrice;
    }

    public function applyModifierForPrice(ProductVariantsPatternModifier $modifier, $price) {
        $language = new ExpressionLanguage();

        if ($modifier->getPrice() !== null) {
            return $language->evaluate($modifier->getPrice(), [
                'price' => $price,
            ]);
        }
        return null;
    }

    public function getMaxPrice(ProductVariantsPattern $pattern){
        
        return 100500;
    }
    
}

