<?php

namespace Furniture\ProductBundle\ProductRemoval;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\RemovalChecker\Removal;
use Furniture\ProductBundle\Entity\ProductPartMaterialOption;

class ProductPartMaterialOptionRemovalChecker
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
     * @param ProductPartMaterialOption $option
     *
     * @return Removal
     */
    public function canHardRemove(ProductPartMaterialOption $option)
    {
        $reasons = [];

        $optionRepository = $this->em->getRepository(ProductPartMaterialOption::class);

        if ($optionRepository->hasValues($option)) {
            $reasons[] = 'Used in values.';
        }

        if (count($reasons)) {
            return new Removal(false, $reasons);
        }

        return new Removal(true);
    }
}
