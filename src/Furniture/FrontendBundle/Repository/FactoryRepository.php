<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FrontendBundle\Repository\Query\FactoryQuery;
use Furniture\ProductBundle\Entity\Product;
use Sylius\Component\Taxonomy\Model\Taxon;

class FactoryRepository
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
     * Find factory by identifier
     *
     * @param int $factory
     *
     * @return Factory|null
     */
    public function find($factory)
    {
        return $this->em->find(Factory::class, $factory);
    }

    /**
     * Find by
     *
     * @param FactoryQuery $query
     *
     * @return Factory[]
     */
    public function findBy(FactoryQuery $query)
    {
        $qb = $this->em->createQueryBuilder()
            ->from(Factory::class, 'f')
            ->select('f');

        if ($query->hasIds()) {
            $qb
                ->andWhere('f.id IN (:ids)')
                ->setParameter('ids', $query->getIds());
        }

        if ($query->hasTaxons()) {
            $orWhere = $qb->expr()->orX();

            foreach ($query->getTaxons() as $key => $taxon) {
                $taxonOrExpr = $qb->expr()->orX();

                $taxons = $this->getChilsForTaxon($taxon);

                foreach ($taxons as $childKey => $childTaxon) {
                    $paramName = sprintf('taxon_%s_%s', $key, $childKey);

                    $taxonOrExpr->add('t.id = :' . $paramName);
                    $qb->setParameter($paramName, $childTaxon->getId());
                }

                $orWhere->add($taxonOrExpr);
            }

            $qb
                ->groupBy('f.id')
                ->innerJoin(Product::class, 'p', 'WITH', 'p.factory = f.id')
                ->leftJoin('p.taxons', 't')
                ->andWhere($orWhere)
                ->having('COUNT(f.id) = :count_taxons')
                ->setParameter('count_taxons', count($query->getTaxons()));
        }

        return $qb
            ->getQuery()
            ->getResult();
    }

    /**
     * Find newest factories
     *
     * @param int $limit
     * @param int $offset
     *
     * @return Factory[]
     */
    public function findNewest($limit = 5, $offset = 0)
    {
        return $this->em->createQueryBuilder()
            ->from(Factory::class, 'f')
            ->select('f')
            ->orderBy('f.createdAt', 'DESC')
            ->getQuery()
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getResult();
    }

    /**
     * Find all
     *
     * @return Factory[]
     */
    public function findAll()
    {
        return $this->findBy(new FactoryQuery());
    }

    /**
     * Get all childs for taxon
     *
     * @param Taxon $taxon
     *
     * @return Taxon[]
     */
    private function getChilsForTaxon(Taxon $taxon)
    {
        $taxons = [$taxon];

        $childs = function (Taxon $taxon, &$taxons) use (&$childs) {
            foreach ($taxon->getChildren() as $childTaxon) {
                $taxons[] = $childTaxon;

                $childs($childTaxon, $taxons);
            }
        };

        $childs($taxon, $taxons);

        return $taxons;
    }
}
