<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\UserBundle\Entity\User;
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
     * @param User            $creator
     *
     * @return Buyer[]
     *
     * @todo: add pagination
     */
    public function findByRetailer(RetailerProfile $retailer, $withCountSpecifications = false, User $creator = null)
    {
        $qb = $this->em->createQueryBuilder()
            ->from(Buyer::class, 'b')
            ->select('b')
            ->innerJoin('b.creator', 'rup', 'WITH', 'rup.retailerProfile = :rp')
            ->setParameter('rp', $retailer);

        if ($withCountSpecifications) {
            $qb
                ->select('b as buyer')
                ->addSelect('COUNT(s.id) as count_specifications')
                ->groupBy('b.id');

            if ($creator) {
                $qb
                    ->leftJoin(Specification::class, 's', 'WITH', 's.buyer = b.id AND s.creator = :creator')
                    ->setParameter('creator', $creator->getRetailerUserProfile());
            } else {
                $qb
                    ->leftJoin(Specification::class, 's', 'WITH', 's.buyer = b.id');
            }
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
}
