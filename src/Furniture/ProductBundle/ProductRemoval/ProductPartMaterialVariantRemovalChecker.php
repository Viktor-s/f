<?php

namespace Furniture\ProductBundle\ProductRemoval;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\RemovalChecker\Removal;
use Furniture\ProductBundle\Entity\ProductPartMaterialVariant;

class ProductPartMaterialVariantRemovalChecker
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
     * @param ProductPartMaterialVariant $variant
     *
     * @return Removal
     */
    public function canHardRemove(ProductPartMaterialVariant $variant)
    {
        $reasonMessages = [];

        /** @var \Furniture\ProductBundle\Entity\Repository\ProductPartMaterialVariantRepository $productPartMaterialVariantRepository */
        $productPartMaterialVariantRepository = $this->em->getRepository(ProductPartMaterialVariant::class);

        if ($productPartMaterialVariantRepository->hasReferencedToProduct($variant)) {
            $reasonMessages[] = 'Has references to product.';
        }

        if (count($reasonMessages)) {
            return new Removal(false, $reasonMessages);
        }

        return new Removal(true);
    }
}
