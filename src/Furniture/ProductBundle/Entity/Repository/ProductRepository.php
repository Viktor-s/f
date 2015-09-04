<?php

namespace Furniture\ProductBundle\Entity\Repository;

use Sylius\Bundle\CoreBundle\Doctrine\ORM\ProductRepository as BaseProductRepositiry;

class ProductRepository extends BaseProductRepositiry {
    
    /**
     * 
     * @param string $name
     * @param integer $limit
     * @param integer $offset
     * @return type
     */
    public function searchNoneBundleByName($name, $limit =5, $offset=0){
        $query_builder = $this->getQueryBuilder();
        return $query_builder
                ->andWhere('translation.name LIKE :name')
                ->setParameter('name', $name.'%')
                ->andWhere('SIZE(product.subProducts) = 0')
                ->getQuery()
                
                ->setMaxResults($limit)
                ->setFirstResult($offset)
                
                ->getResult();
    }
    
    /**
     * 
     * @param string $name
     * @param integer $limit
     * @param integer $offset
     * @return type
     */
    public function searchByName($name, $limit =5, $offset=0){
        $query_builder = $this->getQueryBuilder();
        return $query_builder
                ->andWhere('translation.name LIKE :name')
                ->setParameter('name', $name.'%')
                ->getQuery()
                
                ->setMaxResults($limit)
                ->setFirstResult($offset)
                
                ->getResult();
    }
    
}

