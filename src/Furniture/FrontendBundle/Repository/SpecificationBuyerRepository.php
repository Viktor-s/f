<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\Entity\User;
use Furniture\RetailerBundle\Entity\RetailerProfile;
use Furniture\SpecificationBundle\Entity\Buyer;
use Furniture\SpecificationBundle\Entity\Specification;

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
     * @param bool            $withCountSpecifications
     *
     * @return Buyer[]
     *
     * @todo: add pagination
     */
    public function findByRetailer(RetailerProfile $retailer, $withCountSpecifications = false)
    {
        $qb = $this->em->createQueryBuilder()
            ->from(Buyer::class, 'b')
            ->select('b')
            ->innerJoin('b.creator', 'rup', 'WITH', 'rup.retailerProfile = :rp')
            ->setParameter('rp', $retailer);

        if ($withCountSpecifications) {
            $qb
                ->select('b as buyer')
                ->leftJoin(Specification::class, 's', 'WITH', 's.buyer = b.id')
                ->addSelect('COUNT(s.id) as count_specifications')
                ->groupBy('b.id');
        }

        $result = $qb->getQuery()->getResult();

        if (!$withCountSpecifications) {
            return $result;
        }

        $buyers = [];

        foreach ($result as $item) {
            /** @var Buyer $buyer */
            $buyer = $item['buyer'];
            $buyer->setCountSpecifications($item['count_specifications']);
            $buyers[] = $buyer;
        }

        return $buyers;
    }

    /**
     * Is buyers has specifications?
     *
     * @param Buyer[] $buyers
     *
     * @return array
     */
//    public function hasSpecificationsForBuyers(array $buyers)
//    {
//        $buyerIds = array_map(function (Buyer $buyer) {
//            return $buyer->getId();
//        }, $buyers);
//
//        $qb = $this->em->createQueryBuilder()
//            ->from(Buyer::class, 'b')
//            ->select('b.id as buyer_id')
//            ->innerJoin(Specification::class, 's', 'WITH', 's.buyer = b.id')
//            ->andWhere('b.id IN (:buyer_ids)')
//            ->setParameter('buyer_ids', $buyerIds)
//            ->groupBy('s.id');
//
//        $result = $qb->getQuery()->getArrayResult();
//
//        print_r($result);exit();
//    }
}
