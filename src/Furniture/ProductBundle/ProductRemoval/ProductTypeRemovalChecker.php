<?php

namespace Furniture\ProductBundle\ProductRemoval;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\RemovalChecker\Removal;
use Furniture\ProductBundle\Entity\Type;

class ProductTypeRemovalChecker
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
     * @param Type $type
     *
     * @return Removal
     */
    public function canHardRemove(Type $type)
    {
        $reasons = [];

        $typeRepository = $this->em->getRepository(Type::class);

        if ($typeRepository->hasProducts($type)) {
            $reasons[] = 'Has referenced to products.';
        }

        if (count($reasons)) {
            return new Removal(false, $reasons);
        }

        return new Removal(true);
    }
}
