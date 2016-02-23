<?php

namespace Furniture\ProductBundle\ProductRemoval;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\RemovalChecker\Removal;
use Furniture\ProductBundle\Entity\Style;

class ProductStyleRemovalChecker
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
     * @param Style $style
     *
     * @return Removal
     */
    public function canHardRemove(Style $style)
    {
        $reasons = [];

        $styleRepository = $this->em->getRepository(Style::class);

        if ($styleRepository->hasProducts($style)) {
            $reasons[] = 'Has referenced to products.';
        }

        if (count($reasons)) {
            return new Removal(false, $reasons);
        }

        return new Removal(true);
    }
}
