<?php

namespace Furniture\ProductBundle\Entity\Repository;

use Doctrine\DBAL\Query\QueryBuilder;
use Furniture\ProductBundle\Entity\ProductPartMaterial;
use Sylius\Bundle\TranslationBundle\Doctrine\ORM\TranslatableResourceRepository;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class ProductPartMaterialRepository extends TranslatableResourceRepository
{
    /**
     * Create filter paginator.
     *
     * @param array $criteria
     *
     * @return \Pagerfanta\PagerfantaInterface
     */
    public function createFilterPaginator($criteria = [])
    {
        $qb = parent::getCollectionQueryBuilder();
        $allAliases = $qb->getAllAliases();

        if (!empty($criteria['name'])) {
            $qb
                ->andWhere(
                    $qb->expr()->orX(
                        $qb->expr()->like($qb->expr()->lower($allAliases[0] . '.name'), ':name'),
                        $qb->expr()->like($qb->expr()->lower($allAliases[1] . '.presentation'), ':name')
                    )
                )
                ->setParameter('name', '%' . mb_strtolower($criteria['name']) . '%');
        }

        if (!empty($criteria['factory'])) {
            $qb
                ->andWhere($qb->expr()->eq($allAliases[0] . '.factory', ':factory'))
                ->setParameter('factory', $criteria['factory']);
        }

        return $this->getPaginator($qb);
    }

    /**
     * Has references to product?
     *
     * @param ProductPartMaterial $material
     *
     * @return bool
     */
    public function hasReferencedToProduct(ProductPartMaterial $material)
    {
        $qb = new QueryBuilder($this->_em->getConnection());

        $result = $qb
            ->select('1')
            ->from('product_part_material', 'pm')
            ->innerJoin('pm', 'product_part_material_relation', 'pmr', 'pmr.product_part_material_id = pm.id')
            ->innerJoin('pmr', 'furniture_product_part', 'pp', 'pp.id = pmr.product_part_id')
            ->innerJoin('pp', 'product', 'p', 'p.id = pp.product_id')
            ->andWhere('pm.id = :variant')
            ->setParameter('variant', $material->getId())
            ->setMaxResults(1)
            ->execute()
            ->fetchAll();

        if (count($result)) {
            return true;
        }

        return false;
    }
}
