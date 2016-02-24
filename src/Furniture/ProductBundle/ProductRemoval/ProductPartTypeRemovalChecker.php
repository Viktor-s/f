<?php

namespace Furniture\ProductBundle\ProductRemoval;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\RemovalChecker\Removal;
use Furniture\ProductBundle\Entity\ProductPartType;

class ProductPartTypeRemovalChecker
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
     * @param ProductPartType $type
     *
     * @return Removal
     */
    public function canHardRemove(ProductPartType $type)
    {
        $reason = [];

        $typeRepository = $this->em->getRepository(ProductPartType::class);

        if ($typeRepository->hasReferencesToProducts($type)) {
            $reason[] = 'Has references to product.';
        }

        if (count($reason)) {
            return new Removal(false, $reason);
        }

        return new Removal(true);
    }
}
