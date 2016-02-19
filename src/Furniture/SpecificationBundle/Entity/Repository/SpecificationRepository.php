<?php

namespace Furniture\SpecificationBundle\Entity\Repository;

use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class SpecificationRepository extends EntityRepository
{
    /**
     * Create filter paginator.
     *
     * @param array $criteria
     * @param array $sorting
     *
     * @return \Pagerfanta\PagerfantaInterface
     */
    public function createFilterPaginator(array $criteria = null, array $sorting = null)
    {
        $queryBuilder = parent::getCollectionQueryBuilder();

        if (!empty($criteria['product'])) {
            $queryBuilder
                ->innerJoin('o.items', 'si')
                ->innerJoin('si.skuItem', 'ssi')
                ->innerJoin('ssi.productVariant', 'pv')
                ->innerJoin('pv.object', 'p');

            if (is_numeric($criteria['product']) && strpos($criteria['product'], '.') === false) {
                // Find by id
                $queryBuilder
                    ->andWhere('p.id = :product')
                    ->setParameter('product', $criteria['product']);
            } else {
                // Find by name
                $queryBuilder
                    ->innerJoin('p.translations', 'pt')
                    ->andWhere('LOWER(pt.name) LIKE :product')
                    ->setParameter('product', '%' . mb_strtolower($criteria['product']) . '%');
            }
        }

        if (empty($sorting)) {
            if (!is_array($sorting)) {
                $sorting = [];
            }

            $sorting['createdAt'] = 'desc';
        }

        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }
}
