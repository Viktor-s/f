<?php

namespace Furniture\ProductBundle\ProductRemoval;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\RemovalChecker\Removal;
use Furniture\ProductBundle\Entity\Space;

class ProductSpaceRemovalChecker
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
     * @param Space $space
     *
     * @return Removal
     */
    public function canHardRemove(Space $space)
    {
        $reasons = [];

        $spaceRepository = $this->em->getRepository(Space::class);

        if ($spaceRepository->hasProducts($space)) {
            $reasons[] = 'Has referenced to products.';
        }

        if (count($reasons)) {
            return new Removal(false, $reasons);
        }

        return new Removal(true);
    }
}
