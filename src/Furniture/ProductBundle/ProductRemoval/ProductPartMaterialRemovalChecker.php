<?php

namespace Furniture\ProductBundle\ProductRemoval;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\RemovalChecker\Removal;
use Furniture\ProductBundle\Entity\ProductPartMaterial;

class ProductPartMaterialRemovalChecker
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
     * @param ProductPartMaterial $material
     *
     * @return Removal
     */
    public function canHardRemove(ProductPartMaterial $material)
    {
        $reasonMessages = [];

        /** @var \Furniture\ProductBundle\Entity\Repository\ProductPartMaterialRepository $productPartMaterialRepository */
        $productPartMaterialRepository = $this->em->getRepository(ProductPartMaterial::class);

        if ($productPartMaterialRepository->hasReferencedToProduct($material)) {
            $reasonMessages[] = 'Has references to product.';
        }

        if (count($reasonMessages)) {
            return new Removal(false, $reasonMessages);
        }

        return new Removal(true);
    }
}
