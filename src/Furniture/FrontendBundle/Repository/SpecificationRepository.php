<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\SpecificationBundle\Entity\Buyer;
use Furniture\UserBundle\Entity\User;
use Furniture\FrontendBundle\Repository\Query\SpecificationQuery;
use Furniture\RetailerBundle\Entity\RetailerProfile;
use Furniture\SpecificationBundle\Entity\Specification;

class SpecificationRepository
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
     * Find specification by identifier
     *
     * @param int $specification
     *
     * @return Specification
     */
    public function find($specification)
    {
        return $this->em->createQueryBuilder()
            ->from(Specification::class, 's')
            ->select('s')
            ->andWhere('s.id = :id')
            ->setParameter('id', $specification)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find by
     *
     * @param SpecificationQuery $query
     *
     * @return Specification[]
     *
     * @todo: Add pagination
     */
    public function findBy(SpecificationQuery $query)
    {
        $qb = $this->em->createQueryBuilder()
            ->from(Specification::class, 's')
            ->select('s');

        if ($query->hasRetailer()) {
            $qb
                ->innerJoin('s.creator', 'rup', 'WITH', 'rup.retailerProfile = :rp')
                ->setParameter('rp', $query->getRetailer());
        }

        if ($query->hasUsers()) {
            $ids = array_map(function (User $user) {
                if (!$user->isRetailer()) {
                    throw new \Exception('Only retailer users can be asign to Specification objects');
                }

                return $user->getRetailerUserProfile()->getId();
            }, $query->getUsers());

            $qb
                ->andWhere('s.creator IN (:rup_ids)')
                ->setParameter('rup_ids', $ids);
        }

        if ($query->hasBuyers()) {
            $ids = array_map(function (Buyer $buyer) {
                return $buyer->getId();
            }, $query->getBuyers());

            $qb
                ->innerJoin('s.buyer', 'sb')
                ->andWhere('sb.id IN (:buyer_ids)')
                ->setParameter('buyer_ids', $ids);
        }

        if ($query->hasState()) {
            if ($query->isFinished()) {
                $qb->andWhere('s.finishedAt IS NOT NULL');
            } else if ($query->isOpened()) {
                $qb->andWhere('s.finishedAt IS NULL');
            } else {
                throw new \InvalidArgumentException('Invalid state.');
            }
        }

        return $qb
            ->getQuery()
            ->getResult();
    }

    /**
     * Find opened specifications for retailer
     *
     * @param RetailerProfile $retailer
     *
     * @return Specification[]
     */
    public function findOpenedForRetailer(RetailerProfile $retailer)
    {
        $query = new SpecificationQuery();
        $query
            ->withRetailer($retailer)
            ->opened();

        return $this->findBy($query);
    }

    /**
     * Find finished for retailer
     *
     * @param RetailerProfile $retailer
     *
     * @return Specification[]
     */
    public function findFinishedForRetailer(RetailerProfile $retailer)
    {
        $query = new SpecificationQuery();
        $query
            ->withRetailer($retailer)
            ->finished();

        return $this->findBy($query);
    }

    /**
     * Find opened specifications for user
     *
     * @param User $user
     *
     * @return Specification[]
     */
    public function findOpenedForUser(User $user)
    {
        $query = new SpecificationQuery();
        $query
            ->withUser($user)
            ->opened();

        return $this->findBy($query);
    }

    /**
     * Find closed specification for user
     *
     * @param User $user
     *
     * @return Specification[]
     */
    public function findFinishedForUser(User $user)
    {
        $query = new SpecificationQuery();
        $query
            ->withUser($user)
            ->finished();

        return $this->findBy($query);
    }

    /**
     * Find specifications for user
     *
     * @param User $user
     *
     * @return Specification[]
     */
    public function findForUser(User $user)
    {
        $query = new SpecificationQuery();
        $query
            ->withUser($user);

        return $this->findBy($query);
    }

    /**
     * Find opened specifications for buyer
     *
     * @param User  $creator
     * @param Buyer $buyer
     *
     * @return Specification[]
     */
    public function findOpenedForBuyerAndUser(User $creator, Buyer $buyer)
    {
        $query = new SpecificationQuery();
        $query
            ->withUser($creator)
            ->withBuyer($buyer)
            ->opened();

        return $this->findBy($query);
    }

    /**
     * Find finished specifications for buyer
     *
     * @param User  $creator
     * @param Buyer $buyer
     *
     * @return Specification[]
     */
    public function findFinishedForBuyerAndUser(User $creator, Buyer $buyer)
    {
        $query = new SpecificationQuery();
        $query
            ->withUser($creator)
            ->withBuyer($buyer)
            ->finished();

        return $this->findBy($query);
    }

    /**
     * Find opened specifications for buyer and retailer
     *
     * @param RetailerProfile $retailer
     * @param Buyer           $buyer
     *
     * @return Specification[]
     */
    public function findOpenedForBuyerAndRetailer(RetailerProfile $retailer, Buyer $buyer)
    {
        $query = new SpecificationQuery();
        $query
            ->withRetailer($retailer)
            ->withBuyer($buyer)
            ->opened();

        return $this->findBy($query);
    }

    /**
     * Find finished specifications for buyer and retailer
     *
     * @param RetailerProfile $retailer
     * @param Buyer           $buyer
     *
     * @return Specification[]
     */
    public function findFinishedForBuyerAndRetailer(RetailerProfile $retailer, Buyer $buyer)
    {
        $query = new SpecificationQuery();
        $query
            ->withRetailer($retailer)
            ->withBuyer($buyer)
            ->finished();

        return $this->findBy($query);
    }
}
