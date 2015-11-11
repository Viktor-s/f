<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\Entity\User;
use Furniture\RetailerBundle\Entity\RetailerProfile;
use Furniture\SpecificationBundle\Entity\Buyer;

class SpecificationBuyerRepository
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
     * Find buyer with identifier
     *
     * @param int $buyer
     *
     * @return Buyer
     */
    public function find($buyer)
    {
        return $this->em->createQueryBuilder()
            ->from(Buyer::class, 'b')
            ->select('b')
            ->andWhere('b.id = :id')
            ->setParameter('id', $buyer)
            ->getQuery()
            ->getOneOrNullResult();

    }

    /**
     * Find buyers for user
     *
     * @param RetailerProfile $retailer
     *
     * @return Buyer[]
     *
     * @todo: add pagination
     */
    public function findByRetailer(RetailerProfile $retailer)
    {
        $creators = $retailer->getUsers();
        $ids = array_map(function (User $user) {
            return $user->getId();
        }, $creators->toArray());

        return $this->em->createQueryBuilder()
            ->from(Buyer::class, 'b')
            ->select('b')
            ->andWhere('b.creator IN (:creators)')
            ->setParameter('creators', $ids)
            ->getQuery()
            ->getResult();
    }
}
