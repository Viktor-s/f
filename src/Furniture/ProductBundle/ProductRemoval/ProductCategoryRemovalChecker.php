<?php

namespace Furniture\ProductBundle\ProductRemoval;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\RemovalChecker\Removal;
use Furniture\ProductBundle\Entity\Category;

class ProductCategoryRemovalChecker
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
     * @param Category $category
     *
     * @return Removal
     */
    public function canHardRemove(Category $category)
    {
        $reasons = [];

        $categoryRepository = $this->em->getRepository(Category::class);

        if ($categoryRepository->hasProducts($category)) {
            $reasons[] = 'Has referenced to products.';
        }

        if (count($reasons)) {
            return new Removal(false, $reasons);
        }

        return new Removal(true);
    }
}
