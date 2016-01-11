<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\UserBundle\Entity\User;
use Furniture\RetailerBundle\Entity\RetailerProfile;
use Furniture\RetailerBundle\Entity\RetailerUserProfile;

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
            ->innerJoin('u.retailerUserProfile', 'rup')
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
     * @return \Furniture\UserBundle\Entity\User[]
     */
    public function findForRetailer(RetailerProfile $retailerProfile)
    {
        return $this->em->createQueryBuilder()
            ->from(User::class, 'u')
            ->select('u')
            ->innerJoin('u.retailerUserProfile', 'rup')
            ->andWhere('rup.retailerProfile = :retailer_profile')
            ->setParameter('retailer_profile', $retailerProfile)
            ->orderBy('u.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
