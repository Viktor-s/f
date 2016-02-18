<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FrontendBundle\Repository\Query\FactoryQuery;
use Furniture\FactoryBundle\Entity\RetailerFactoryRate;
use Furniture\RetailerBundle\Entity\RetailerProfile;
use Furniture\RetailerBundle\Entity\RetailerUserProfile;

class RetailerFactoryRateRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var FactoryRepository
     */
    private $factoryRepository;

    /**
     * Construct
     *
     * @param EntityManagerInterface $em
     * @param FactoryRepository      $factoryRepository
     */
    public function __construct(EntityManagerInterface $em, FactoryRepository $factoryRepository)
    {
        $this->em = $em;
        $this->factoryRepository = $factoryRepository;
    }

    /**
     * Find rate by identifier
     *
     * @param int $rate
     *
     * @return RetailerFactoryRate|null
     */
    public function find($rate)
    {
        return $this->em->createQueryBuilder()
            ->from(RetailerFactoryRate::class, 'rfr')
            ->select('rfr')
            ->andWhere('rfr.id = :rate')
            ->setParameter('rate', $rate)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Find rates by user
     *
     * @param RetailerUserProfile $retailerUserProfile
     *
     * @return RetailerFactoryRate[]
     */
    public function findByRetailerUserProfile(RetailerUserProfile $retailerUserProfile)
    {
        return $this->em->createQueryBuilder()
            ->from(RetailerFactoryRate::class, 'rfr')
            ->select('rfr')
            ->innerJoin('rfr.retailer', 'r')
            ->innerJoin('rfr.factory', 'f')
            ->innerJoin('r.retailerUserProfiles', 'rup')
            ->andWhere('rup.id = :retailerUserProfile')
            ->andWhere('f.enabled = :enabled')
            ->setParameter('retailerUserProfile', $retailerUserProfile->getId())
            ->setParameter('enabled', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * Find rates by retailer
     *
     * @param RetailerProfile $retailer
     *
     * @return RetailerFactoryRate[]
     */
    public function findByRetailer(RetailerProfile $retailer)
    {
        return $this->em->createQueryBuilder()
            ->from(RetailerFactoryRate::class, 'rfr')
            ->innerJoin('rfr.retailer', 'r')
            ->innerJoin('rfr.factory', 'f')
            ->andWhere('r.id = :retailer')
            ->andWhere('f.enabled = :enabled')
            ->setParameter('retailer', $retailer->getId())
            ->setParameter('enabled', true)
            ->getQuery()
            ->getResult();
    }

    /**
     * Has factories for create condition?
     *
     * @param RetailerProfile $retailerProfile
     *
     * @return bool
     */
    public function hasFactoriesForCreateCondition(RetailerProfile $retailerProfile)
    {
        $factoryQuery = new FactoryQuery();
        $factoryQuery->withRetailer($retailerProfile);

        if ($retailerProfile->isDemo()) {
            $factoryQuery->withoutOnlyEnabledOrDisabled();
        }

        $qb = $this->factoryRepository->createQueryBuilderForFactory($factoryQuery);

        $qb
            ->leftJoin(RetailerFactoryRate::class, 'rfr', 'with', 'rfr.factory = f.id AND rfr.retailer = :retailer')
            ->andWhere('rfr.retailer is NULL')
            ->setParameter('retailer', $retailerProfile);

        $qb->select('COUNT(f)');

        return (bool) $qb->getQuery()->getSingleScalarResult();
    }
}
