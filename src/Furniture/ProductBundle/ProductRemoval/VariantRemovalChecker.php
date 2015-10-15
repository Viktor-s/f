<?php

namespace Furniture\ProductBundle\ProductRemoval;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\ProductBundle\Entity\ProductVariant;

class VariantRemovalChecker
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
     * @param ProductVariant $variant
     *
     * @return VariantRemoval
     */
    public function canHardRemove(ProductVariant $variant)
    {
        $reasonMessages = [];

        /** @var \Furniture\ProductBundle\Entity\Repository\ProductVariantRepository $variantRepository */
        $variantRepository = $this->em->getRepository(ProductVariant::class);

        if ($variantRepository->hasSpecificationItems($variant)) {
            $reasonMessages[] = 'The variant have a specification items.';
        }

        if (count($reasonMessages)) {
            return new VariantRemoval(false, $reasonMessages);
        }

        return new VariantRemoval(true);
    }
}
