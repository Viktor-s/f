<?php

namespace Furniture\UserBundle\Entity\Repository;

use Sylius\Bundle\UserBundle\Doctrine\ORM\CustomerRepository as BaseCustomerRepository;

class CustomerRepository extends BaseCustomerRepository
{
    /**
     * {@inheritDoc}
     */
    public function createFilterPaginator($criteria = [], $sorting = [], $deleted = false)
    {
        $queryBuilder = parent::getCollectionQueryBuilder()
            ->leftJoin($this->getPropertyName('user'), 'user')
            ->leftJoin('user.retailerUserProfile', 'rup')
            ->leftJoin('rup.retailerProfile', 'rp');

        if ($deleted) {
            $this->_em->getFilters()->disable('softdeleteable');
        }

        if (isset($criteria['query'])) {
            $queryBuilder
                ->where($queryBuilder->expr()->like($this->getPropertyName('emailCanonical'), ':query'))
                ->orWhere($queryBuilder->expr()->like($this->getPropertyName('firstName'), ':query'))
                ->orWhere($queryBuilder->expr()->like($this->getPropertyName('lastName'), ':query'))
                ->orWhere($queryBuilder->expr()->like('user.username', ':query'))
                ->setParameter('query', '%'.$criteria['query'].'%')
            ;
        }

        if (isset($criteria['enabled'])) {
            $queryBuilder
                ->andWhere($queryBuilder->expr()->eq('user.enabled', ':enabled'))
                ->setParameter('enabled', $criteria['enabled'])
            ;
        }

        if (!empty($criteria['mode'])) {
            if ($criteria['mode'] == 'retailer') {
                $queryBuilder->andWhere('rp.id IS NOT NULL');
            }
        }

        if (!empty($criteria['retailerId'])) {
            $queryBuilder
                ->andWhere('rp.id = :retailer_id')
                ->setParameter('retailer_id', $criteria['retailerId']);
        }

        if (!empty($criteria['factoryId'])) {
            $queryBuilder
                ->leftJoin('user.factory', 'fp')
                ->andWhere('fp.id = :factory_id')
                ->setParameter('factory_id', $criteria['factoryId']);
        }

        if (empty($sorting)) {
            if (!is_array($sorting)) {
                $sorting = array();
            }
            $sorting['updatedAt'] = 'desc';
        }

        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }
}
