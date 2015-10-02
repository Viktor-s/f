<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\Entity\User;
use Furniture\FactoryBundle\Entity\UserFactoryRate;

class UserFactoryRateRepository
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
     * @return UserFactoryRate|null
     */
    public function find($rate)
    {
        return $this->em->createQueryBuilder()
            ->from(UserFactoryRate::class, 'ufr')
            ->select('ufr')
            ->andWhere('ufr.id = :rate')
            ->setParameter('rate', $rate)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find rates by user
     *
     * @param User $user
     *
     * @return UserFactoryRate[]
     */
    public function findByUser(User $user)
    {
        return $this->em->createQueryBuilder()
            ->from(UserFactoryRate::class, 'ufr')
            ->select('ufr')
            ->andWhere('ufr.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
            ->getResult();
    }
}
