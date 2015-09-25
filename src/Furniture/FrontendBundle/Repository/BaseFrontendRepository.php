<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\ProductBundle\Entity\Product;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class BaseFrontendRepository extends EntityRepository
{

    function __construct($em, $entityName) {
        $metadata = $em->getClassMetadata($entityName);
        parent::__construct($em, $metadata);
    }
    
    /**
     * @param QueryBuilder $queryBuilder
     *
     * @return Pagerfanta
     */
    public function getPaginator(QueryBuilder $queryBuilder)
    {
        return new Pagerfanta(new DoctrineORMAdapter($queryBuilder, true, false));
    }
    
}