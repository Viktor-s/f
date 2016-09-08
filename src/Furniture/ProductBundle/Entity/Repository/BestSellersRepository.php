<?php

namespace Furniture\ProductBundle\Entity\Repository;

use Furniture\ProductBundle\Entity\BestSellersSet;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class BestSellersRepository extends EntityRepository
{
    public function updateActiveSet($setId)
    {
        $this->createQueryBuilder('ppi')
            ->update(BestSellersSet::class, 'bss')
            ->set('bss.active', ':active')
            ->setParameter('active', false)
            ->getQuery()
            ->execute();

        return $this->createQueryBuilder('ppi')
            ->update(BestSellersSet::class, 'bss')
            ->set('bss.active', ':active')
            ->where('bss.id = :setId')
            ->setParameters(['active' => true, 'setId' => $setId])
            ->getQuery()
            ->execute();
    }
}