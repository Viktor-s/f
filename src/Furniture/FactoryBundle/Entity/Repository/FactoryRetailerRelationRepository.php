<?php

namespace Furniture\FactoryBundle\Entity\Repository;

use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class FactoryRetailerRelationRepository extends EntityRepository
{
    /**
     * Create filter paginator.
     *
     * @param array $criteria
     * @param array $sorting
     *
     * @return \Pagerfanta\PagerfantaInterface
     */
    public function createPaginator(array $criteria = [], array $sorting = [])
    {
        $status = null;

        if (isset($criteria['status'])) {
            $status = $criteria['status'];
            unset($criteria['status']);
        }

        $queryBuilder = $this->getCollectionQueryBuilder();

        if ($status) {
            switch ($status) {
                case 'wait':
                    $criteria['active'] = true;
                    $queryBuilder->andWhere(
                        $queryBuilder->expr()->orX(
                            $queryBuilder->expr()->eq($this->getPropertyName('factoryAccept'), 'false'),
                            $queryBuilder->expr()->eq($this->getPropertyName('retailerAccept'), 'false')
                        )
                    );
                    break;
                case 'approve':
                    $criteria['active'] = true;
                    $criteria['factoryAccept'] = true;
                    $criteria['retailerAccept'] = true;
                    break;
                case 'decline':
                    $criteria['active'] = false;
                    break;
            }
        }

        $sorting['id'] = 'ASC';

        $this->applyCriteria($queryBuilder, $criteria);
        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }
}
