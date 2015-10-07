<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\Entity\User;
use Furniture\FactoryBundle\Entity\FactoryUserRelation;

class FactoryUserRelationRepository
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
     * @return FactoryUserRelation|null
     */
    public function find($relation)
    {
        return $this->em->createQueryBuilder()
            ->from(FactoryUserRelation::class, 'fur')
            ->select('fur')
            ->andWhere('fur.id = :relation')
            ->setParameter('relation', $relation)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find user requests to factory for factory
     *
     * @param User $user
     *
     * @return FactoryUserRelation[]
     */
    public function findUserRequestsForFactory(User $user)
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
     * @return FactoryUserRelation[]
     */
    public function findRequestToUsersForFactory(User $user)
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
     * @return FactoryUserRelation[]
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
     * @param User $user
     *
     * @return FactoryUserRelation[]
     */
    public function findFactoryRequestsForUser(User $user)
    {
        return $this->createQueryBuilderForRequestsForUser($user, false, true)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find requests from user to factory
     *
     * @param User $user
     *
     * @return FactoryUserRelation[]
     */
    public function findRequestsToFactoriesForUser(User $user)
    {
        return $this->createQueryBuilderForRequestsForUser($user, true, false)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find authorized relations for user
     *
     * @param User $user
     *
     * @return FactoryUserRelation[]
     */
    public function findAuthorizedForUser(User $user)
    {
        return $this->createQueryBuilderForRequestsForUser($user, true, true)
            ->getQuery()
            ->getResult();
    }

    /**
     * Create a query builder for factory user relations
     *
     * @param User $user
     * @param bool $userAccept
     * @param bool $factoryAccept
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    private function createQueryBuilderForRequestsForFactory(User $user, $userAccept, $factoryAccept)
    {
        return $this->em->createQueryBuilder()
            ->from(FactoryUserRelation::class, 'fur')
            ->select('fur')
            ->innerJoin('fur.factory', 'f')
            ->innerJoin('f.users', 'fu')
            ->andWhere('fu.id = :user')
            ->andWhere('fur.userAccept = :user_accept')
            ->andWhere('fur.factoryAccept = :factory_accept')
            ->setParameters([
                'user' => $user,
                'user_accept' => $userAccept,
                'factory_accept' => $factoryAccept
            ]);
    }

    /**
     * Create a query builder for user relations
     *
     * @param User $user
     * @param bool $userAccept
     * @param bool $factoryAccept
     *
     * @return \Doctrine\ORM\QueryBuilder
     */
    public function createQueryBuilderForRequestsForUser(User $user, $userAccept, $factoryAccept)
    {
        return $this->em->createQueryBuilder()
            ->from(FactoryUserRelation::class, 'fur')
            ->select('fur')
            ->innerJoin('fur.user', 'u')
            ->andWhere('u.id = :user')
            ->andWhere('fur.userAccept = :user_accept')
            ->andWhere('fur.factoryAccept = :factory_accept')
            ->setParameters([
                'user' => $user,
                'factory_accept' => $factoryAccept,
                'user_accept' => $userAccept
            ]);
    }
}
