<?php

namespace Furniture\ProductBundle\ProductRemoval;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\ProductBundle\Entity\Product;

/**
 * Service for check products before remove
 */
class ProductRemovalChecker
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Construct
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Can hard remove?
     *
     * @param Product $product
     *
     * @return ProductRemoval
     */
    public function canHardRemove(Product $product)
    {
        $reasonMessages = [];

        /** @var \Furniture\ProductBundle\Entity\Repository\ProductRepository $productRepository */
        $productRepository = $this->em->getRepository(Product::class);

        if ($productRepository->hasSpecificationItems($product)) {
            $reasonMessages[] = 'The product have a specification items.';
        }

        if (count($reasonMessages)) {
            return new ProductRemoval(false, $reasonMessages);
        }

        return new ProductRemoval(true);
    }

    /**
     * Can remove?
     *
     * @param Product $product
     *
     * @return ProductRemoval
     */
    public function canRemove(Product $product)
    {
        $reasonMessages = [];

        /** @var \Furniture\ProductBundle\Entity\Repository\ProductRepository $productRepository */
        $productRepository = $this->em->getRepository(Product::class);

        if ($productRepository->hasSpecificationItems($product)) {
            $reasonMessages[] = 'The product have a specification items.';
        }

        if ($product->hasVariantsWithoutMaster()) {
            $reasonMessages[] = 'The product have a SKU.';
        }

        if (count($reasonMessages)) {
            return new ProductRemoval(false, $reasonMessages);
        }

        return new ProductRemoval(true);
    }
}
