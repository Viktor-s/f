<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
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
     * Find relation between factory and retailer
     *
     * @param Factory         $factory
     * @param RetailerProfile $retailerProfile
     *
     * @return FactoryRetailerRelation|null
     */
    public function findRelationBetweenFactoryAndRetailer(Factory $factory, RetailerProfile $retailerProfile)
    {
        return $this->em->createQueryBuilder()
            ->from(FactoryRetailerRelation::class, 'frr')
            ->select('frr')
            ->andWhere('frr.factory = :factory')
            ->andWhere('frr.retailer = :retailer')
            ->setParameters([
                'retailer' => $retailerProfile,
                'factory'  => $factory,
            ])
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
                'user'            => $user,
                'retailer_accept' => $retailerAccept,
                'factory_accept'  => $factoryAccept,
            ]);
    }

    /**
     * Has factories for create relation between retailer and factory
     *
     * @param RetailerProfile $retailer
     *
     * @return bool
     */
    public function hasFactoriesForCreateRelationFromRetailerToFactory(RetailerProfile $retailer)
    {
        $existRelationQb = new QueryBuilder($this->em->getConnection());
        $existRelationQb
            ->select('frr.factory_id')
            ->from('factory_user_relation', 'frr')
            ->andWhere('frr.retailer_id = :retailer_id');

        $qb = new QueryBuilder($this->em->getConnection());
        $qb
            ->select('1')
            ->from('factory', 'f')
            ->andWhere('f.enabled IS TRUE')
            ->andWhere('f.id NOT IN (' . $existRelationQb->getSQL() . ')')
            ->setParameter('retailer_id', $retailer->getId());

        $stmt = $qb->execute();
        $result = $stmt->fetchAll();

        if (count($result)) {
            return true;
        }

        return false;
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
    private function createQueryBuilderForRequestsForRetailer(RetailerProfile $retailer, $retailerAccept, $factoryAccept)
    {
        return $this->em->createQueryBuilder()
            ->from(FactoryRetailerRelation::class, 'frr')
            ->select('frr')
            ->innerJoin('frr.retailer', 'r')
            ->innerJoin('frr.factory', 'f')
            ->andWhere('r.id = :retailer')
            ->andWhere('f.enabled = :enabled')
            ->andWhere('frr.retailerAccept = :retailer_accept')
            ->andWhere('frr.factoryAccept = :factory_accept')
            ->setParameters(
                [
                    'retailer'        => $retailer->getId(),
                    'enabled'         => true,
                    'factory_accept'  => $factoryAccept,
                    'retailer_accept' => $retailerAccept,
                ]
            );
    }
}
