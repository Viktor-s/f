<?php

namespace Furniture\FactoryBundle\Entity\Repository;

use Doctrine\ORM\EntityRepository;
use Furniture\CommonBundle\Entity\User;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FactoryBundle\Entity\UserFactoryRate;

class UserFactoryRateRepository extends EntityRepository
{
    /**
     * Find user factory rate for factory and user
     *
     * @param Factory $factory
     * @param User    $user
     *
     * @return UserFactoryRate
     */
    public function findByFactoryAndUser(Factory $factory, User $user)
    {
        return $this->_em->createQueryBuilder()
            ->from(UserFactoryRate::class, 'ufr')
            ->select('ufr')
            ->andWhere('ufr.factory = :factory')
            ->andWhere('ufr.user = :user')
            ->setParameters([
                'factory' => $factory,
                'user' => $user
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
