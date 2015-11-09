<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\Entity\User;
use Furniture\RetailerBundle\Entity\RetailerProfile;

class RetailerEmployeeRepository
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
     * Find by id
     *
     * @param int $id
     *
     * @return User
     */
    public function find($id)
    {
        return $this->em->createQueryBuilder()
            ->from(User::class, 'u')
            ->select('u')
            ->andWhere('u.retailerProfile IS NOT NULL')
            ->andWhere('u.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find for retailer
     *
     * @param RetailerProfile $retailerProfile
     *
     * @return User[]
     */
    public function findForRetailer(RetailerProfile $retailerProfile)
    {
        return $this->em->createQueryBuilder()
            ->from(User::class, 'u')
            ->select('u')
            ->andWhere('u.retailerProfile = :retailer_profile')
            ->setParameter('retailer_profile', $retailerProfile)
            ->orderBy('u.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
