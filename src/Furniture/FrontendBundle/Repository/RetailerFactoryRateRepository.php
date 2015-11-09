<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\Entity\User;
use Furniture\FactoryBundle\Entity\RetailerFactoryRate;
use Furniture\RetailerBundle\Entity\RetailerProfile;

class RetailerFactoryRateRepository
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
     * Find rate by identifier
     *
     * @param int $rate
     *
     * @return RetailerFactoryRate|null
     */
    public function find($rate)
    {
        return $this->em->createQueryBuilder()
            ->from(RetailerFactoryRate::class, 'rfr')
            ->select('rfr')
            ->andWhere('rfr.id = :rate')
            ->setParameter('rate', $rate)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find rates by user
     *
     * @param User $user
     *
     * @return RetailerFactoryRate[]
     */
    public function findByUser(User $user)
    {
        return $this->em->createQueryBuilder()
            ->from(RetailerFactoryRate::class, 'rfr')
            ->select('rfr')
            ->innerJoin('rfr.retailer', 'r')
            ->innerJoin('r.users', 'u')
            ->andWhere('u.id = :user')
            ->setParameter('user', $user->getId())
            ->getQuery()
            ->getResult();
    }

    /**
     * Find rates by retailer
     *
     * @param RetailerProfile $retailer
     *
     * @return RetailerFactoryRate[]
     */
    public function findByRetailer(RetailerProfile $retailer)
    {
        return $this->em->createQueryBuilder()
            ->from(RetailerFactoryRate::class, 'rfr')
            ->innerJoin('rfr.retailer', 'r')
            ->andWhere('r.id = :retailer')
            ->setParameter('retailer', $retailer->getId())
            ->getQuery()
            ->getResult();
    }
}
