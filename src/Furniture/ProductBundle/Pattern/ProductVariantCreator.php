<?php

namespace Furniture\ProductBundle\Pattern;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\ProductBundle\Entity\ProductPartVariantSelection;
use Furniture\ProductBundle\Entity\ProductVariant;
use Furniture\ProductBundle\Entity\ProductVariantsPattern;
use Furniture\ProductBundle\Pattern\Finder\ProductVariantFinder;

class ProductVariantCreator
{
    /**
     * @var ProductVariantFinder
     */
    private $skuFinder;

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
    public function __construct(ProductVariantFinder $finder, EntityManagerInterface $em)
    {
        $this->skuFinder = $finder;
        $this->em = $em;
    }

    /**
     * Create or load SKU (Product variant) by parameters and pattern
     *
     * @param ProductVariantsPattern   $pattern
     * @param ProductVariantParameters $parameters
     *
     * @return \Furniture\ProductBundle\Entity\ProductVariant
     *
     * @todo: add control deleted variants?
     */
    public function create(ProductVariantsPattern $pattern, ProductVariantParameters $parameters)
    {
        // Try load SKU from storage
        $variant = $this->skuFinder->find($parameters);

        if ($variant) {
            // SKU exist. Not create
            return $variant;
        }

        // SKU not exist. Must create new.
        $variant = new ProductVariant();
        $variant->setProduct($pattern->getProduct());
        $variant->setPrice($pattern->getPrice());

        foreach ($parameters->getMaterialVariantSelections() as $materialVariantSelection) {
            $selection = new ProductPartVariantSelection();
            $selection
                ->setProductPart($materialVariantSelection->getProductPart())
                ->setProductPartMaterialVariant($materialVariantSelection->getMaterialVariant())
                ->setProductVariant($variant);

            $variant->addProductPartVariantSelection($selection);
        }

        if(count($parameters->getSkuOptionVariantSelections())){
            foreach($parameters->getSkuOptionVariantSelections() as $skuOptionVariant){
                $variant->addSkuOption($skuOptionVariant);
            }
        }
        
        if ($parameters->getProductScheme()) {
            $variant->setProductScheme($parameters->getProductScheme());
        }

        $this->em->persist($variant);
        $this->em->flush($variant);

        return $variant;
    }
}
