<?php

namespace Furniture\ProductBundle\ProductRemoval;

use Doctrine\ORM\EntityManagerInterface;
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
     * @return AttributeRemoval
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
            return new AttributeRemoval(false, $reasonMessages);
        }

        return new AttributeRemoval(true);
    }
}
