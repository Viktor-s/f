<?php

namespace Furniture\FactoryBundle\FactoryRemoval;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FactoryBundle\Entity\Factory;

class FactoryRemovalChecker
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
     * Can hard remove factory?
     *
     * @param Factory $factory
     *
     * @return FactoryRemoval
     */
    public function canHardRemove(Factory $factory)
    {
        $reasons = [];

        /** @var \Furniture\FactoryBundle\Entity\Repository\FactoryRepository $factoryRepository */
        $factoryRepository = $this->em->getRepository(Factory::class);

        if ($factoryRepository->hasProducts($factory)) {
            $reasons[] = 'Has products.';
        }

        if ($factoryRepository->hasCustomers($factory)) {
            $reasons[] = 'Has customers.';
        }

        if ($factoryRepository->hasProductPartMaterials($factory)) {
            $reasons[] = 'Has product part materials.';
        }

        if (count($reasons)) {
            return new FactoryRemoval(false, $reasons);
        }

        return new FactoryRemoval(true);
    }
}
