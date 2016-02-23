<?php

namespace Furniture\ProductBundle\Entity\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Furniture\ProductBundle\Entity\Category;
use Sylius\Bundle\TranslationBundle\Doctrine\ORM\TranslatableResourceRepository;

class CategoryRepository extends TranslatableResourceRepository
{
    /**
     * Has products by space?
     *
     * @param Category $category
     *
     * @return bool
     */
    public function hasProducts(Category $category)
    {
        $qb = new QueryBuilder($this->_em->getConnection());

        $qb
            ->select('1')
            ->from('product_categories', 'pc')
            ->andWhere('pc.category_id = :category_id')
            ->setParameter('category_id', $category->getId())
            ->setMaxResults(1);

        $stmt = $qb->execute();
        $result = $stmt->fetchAll();

        if (count($result)) {
            return true;
        }

        return false;
    }
}
