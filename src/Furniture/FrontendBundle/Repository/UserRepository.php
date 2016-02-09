<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\UserBundle\Entity\User;

class UserRepository
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
     * Find user by username
     *
     * @param string $username
     *
     * @return User|null
     */
    public function findByUsername($username)
    {
        return $this->em->createQueryBuilder()
            ->from(User::class, 'u')
            ->select('u')
            ->andWhere('u.usernameCanonical = :username')
            ->setParameter('username', User::canonizeUsername($username))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find user by confirmation token
     *
     * @param string $token
     *
     * @return User|null
     */
    public function findByConfirmationToken($token)
    {
        return $this->em->createQueryBuilder()
            ->from(User::class, 'u')
            ->select('u')
            ->andWhere('u.confirmationToken = :token')
            ->setParameter('token', $token)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
