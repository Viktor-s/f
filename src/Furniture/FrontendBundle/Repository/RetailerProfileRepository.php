<?php
namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\RetailerBundle\Entity\RetailerProfile;

class RetailerProfileRepository
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
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile
     */
    public function findById($id)
    {
        return $this->em->createQueryBuilder()
            ->from(RetailerProfile::class, 'rp')
            ->select('rp')
            ->andWhere('rp.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getOneOrNullResult();
    }
    
    /**
     * 
     * @return \Furniture\RetailerBundle\Entity\RetailerProfile[]
     */
    public function findAll()
    {
        return $this->em->createQueryBuilder()
            ->from(RetailerProfile::class, 'rp')
            ->select('rp')
            ->getQuery()
            ->getResult();
    }
}
