<?php

namespace Furniture\ProductBundle\ProductRemoval;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\RemovalChecker\Removal;
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
     * @return Removal
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
            return new Removal(false, $reasonMessages);
        }

        return new Removal(true);
    }

    /**
     * Can remove?
     *
     * @param ProductVariant $variant
     *
     * @return Removal
     */
    public function canRemove(ProductVariant $variant)
    {
        $reasonMessages = [];

        /** @var \Furniture\ProductBundle\Entity\Repository\ProductVariantRepository $variantRepository */
        $variantRepository = $this->em->getRepository(ProductVariant::class);

        if ($variantRepository->hasSpecificationItems($variant)) {
            $reasonMessages[] = 'The variant have a specification items.';
        }

        if (count($reasonMessages)) {
            return new Removal(false, $reasonMessages);
        }

        return new Removal(true);
    }
}
