<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\UserBundle\Entity\User;
use Furniture\FactoryBundle\Entity\FactoryRetailerRelation;
use Furniture\RetailerBundle\Entity\RetailerProfile;
use Furniture\FactoryBundle\Entity\Factory;

class FactoryRetailerRelationRepository
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
     * Find relation by identifier
     *
     * @param int $relation
     *
     * @return FactoryRetailerRelation|null
     */
    public function find($relation)
    {
        return $this->em->createQueryBuilder()
            ->from(FactoryRetailerRelation::class, 'fur')
            ->select('fur')
            ->andWhere('fur.id = :relation')
            ->setParameter('relation', $relation)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find user requests to factory for factory
     *
     * @param \Furniture\UserBundle\Entity\User $user
     *
     * @return FactoryRetailerRelation[]
     */
    public function findRetailerRequestsForFactory(User $user)
    {
        return $this->createQueryBuilderForRequestsForFactory($user, true, false)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find requests from factory to user for factory
     *
     * @param User $user
     *
     * @return FactoryRetailerRelation[]
     */
    public function findRequestToRetailersForFactory(User $user)
    {
        return $this->createQueryBuilderForRequestsForFactory($user, false, true)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find authorized users for factory
     *
     * @param User $user
     *
     * @return FactoryRetailerRelation[]
     */
    public function findAuthorizedForFactory(User $user)
    {
        return $this->createQueryBuilderForRequestsForFactory($user, true, true)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find request from factory to user
     *
     * @param RetailerProfile $retailer
     *
     * @return FactoryRetailerRelation[]
     */
    public function findFactoryRequestsForRetailer(RetailerProfile $retailer)
    {
        return $this->createQueryBuilderForRequestsForRetailer($retailer, false, true)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find requests from user to factory
     *
     * @param RetailerProfile $retailer
     *
     * @return FactoryRetailerRelation[]
     */
    public function findRequestsToFactoriesForRetailer(RetailerProfile $retailer)
    {
        return $this->createQueryBuilderForRequestsForRetailer($retailer, true, false)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find authorized relations for user
     *
     * @param RetailerProfile $retailer
     *
     * @return FactoryRetailerRelation[]
     */
    public function findAuthorizedForRetailer(RetailerProfile $retailer)
    {
        return $this->createQueryBuilderForRequestsForRetailer($retailer, true, true)
            ->getQuery()
            ->getResult();
    }

    /**
     * Create a query builder for factory user relations
     *
     * @param User $user
     * @param bool $retailerAccept
     * @param bool $factoryAccept
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function createQueryBuilderForRequestsForFactory(User $user, $retailerAccept, $factoryAccept)
    {
        return $this->em->createQueryBuilder()
            ->from(FactoryRetailerRelation::class, 'fur')
            ->select('fur')
            ->innerJoin('fur.factory', 'f')
            ->innerJoin('f.users', 'fu')
            //If visible in front!
            ->andWhere('f.enabled = true')
            ->andWhere('fu.id = :user')
            ->andWhere('fur.retailerAccept = :retailer_accept')
            ->andWhere('fur.factoryAccept = :factory_accept')
            ->setParameters([
                'user'           => $user,
                'retailer_accept'    => $retailerAccept,
                'factory_accept' => $factoryAccept,
            ]);
    }

    /**
     * Create a query builder for user relations
     *
     * @param RetailerProfile $retailer
     * @param bool            $retailerAccept
     * @param bool            $factoryAccept
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createQueryBuilderForRequestsForRetailer(RetailerProfile $retailer, $retailerAccept, $factoryAccept)
    {
        return $this->em->createQueryBuilder()
            ->from(FactoryRetailerRelation::class, 'frr')
            ->select('frr')
            ->innerJoin('frr.retailer', 'r')
            ->andWhere('r.id = :retailer')
            ->andWhere('frr.retailerAccept = :retailer_accept')
            ->andWhere('frr.factoryAccept = :factory_accept')
            ->setParameters([
                'retailer'       => $retailer->getId(),
                'factory_accept' => $factoryAccept,
                'retailer_accept'    => $retailerAccept,
            ]);
    }
    
    public function findRequestBetweenRetailerFactry( RetailerProfile $retailer, Factory $factory ){
        return $this->em->createQueryBuilder()
                ->from(FactoryRetailerRelation::class, 'frr')
                ->select('frr')
                ->andWhere('frr.factory = :factory')
                ->andWhere('frr.retailer = :retailer')
                ->setParameter('factory', $factory)
                ->setParameter('retailer', $retailer)
                ->getQuery()
                ->getOneOrNullResult();
                
    }
}
