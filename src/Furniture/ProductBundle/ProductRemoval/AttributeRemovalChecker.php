<?php

namespace Furniture\ProductBundle\ProductRemoval;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\RemovalChecker\Removal;
use Sylius\Component\Product\Model\Attribute;

class AttributeRemovalChecker
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
     * Can remove product attribute?
     *
     * @param Attribute $attribute
     *
     * @return Removal
     */
    public function canRemove(Attribute $attribute)
    {
        $reasonMessages = [];

        /** @var \Furniture\ProductBundle\Entity\Repository\AttributeRepository $attributeRepository */
        $attributeRepository = $this->em->getRepository(Attribute::class);

        if ($attributeRepository->hasReferencedToProduct($attribute)) {
            $reasonMessages[] = 'Has references to product.';
        }

        if (count($reasonMessages)) {
            return new Removal(false, $reasonMessages);
        }

        return new Removal(true);
    }
}
