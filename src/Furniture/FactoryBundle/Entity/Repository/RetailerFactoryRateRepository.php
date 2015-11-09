<?php

namespace Furniture\FactoryBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Furniture\CommonBundle\Entity\User;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FactoryBundle\Entity\RetailerFactoryRate;
use Furniture\RetailerBundle\Entity\RetailerProfile;

class RetailerFactoryRateRepository extends EntityRepository
{
    /**
     * Find user factory rate for factory and retailer
     *
     * @param Factory $factory
     * @param RetailerProfile    $retailer
     *
     * @return RetailerFactoryRate
     */
    public function findByFactoryAndRetailer(Factory $factory, RetailerProfile $retailer)
    {
        return $this->_em->createQueryBuilder()
            ->from(RetailerFactoryRate::class, 'rfr')
            ->select('rfr')
            ->andWhere('rfr.factory = :factory')
            ->andWhere('rfr.retailer = :retailer')
            ->setParameters([
                'factory' => $factory,
                'retailer' => $retailer
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
