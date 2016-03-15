<?php

namespace Furniture\ProductBundle\ProductRemoval;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\RemovalChecker\Removal;
use Furniture\SkuOptionBundle\Entity\SkuOptionType;

class SkuOptionTypeRemovalChecker
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
     * Can remove skuOptionType?
     *
     * @param SkuOptionType $skuOption
     *
     * @return Removal
     */
    public function canRemove(SkuOptionType $skuOption)
    {
        $reasonMessages = [];

        /** @var \Furniture\SkuOptionBundle\Entity\Repository\SkuOptionTypeRepository $skuOptionRepository */
        $skuOptionRepository = $this->em->getRepository(SkuOptionType::class);

        if ($skuOptionRepository->hasReferencedToProduct($skuOption)) {
            $reasonMessages[] = 'Has references to product.';
        }

        if (count($reasonMessages)) {
            return new Removal(false, $reasonMessages);
        }

        return new Removal(true);
    }
}
