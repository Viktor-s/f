<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FrontendBundle\Repository\Query\FactoryQuery;
use Furniture\ProductBundle\Entity\Product;
use Sylius\Component\Core\Model\Taxon;

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

        if ($query->hasTaxons()) {
            $factoryIdsByTaxon = [];
            $factoryAllIds = [1];

            foreach ($query->getTaxons() as $taxon) {
                $ids = $this->findFactoryIdsByTaxon($taxon);
                $factoryAllIds = array_merge($factoryAllIds, $ids);
                $factoryIdsByTaxon[] = $ids;
            }

            $arguments = array_merge([$factoryAllIds], $factoryIdsByTaxon);
            $ids = call_user_func_array('array_intersect', $arguments);

            if (!count($ids)) {
                return [];
            }

            $query->withIds($ids);
        }

        if ($query->hasIds()) {
            $qb
                ->andWhere('f.id IN (:ids)')
                ->setParameter('ids', $query->getIds());
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
     * Get factory IDs by taxon
     *
     * @param Taxon $taxon
     *
     * @return array
     */
    private function findFactoryIdsByTaxon(Taxon $taxon)
    {
        $taxons = $this->getChildsForTaxon($taxon);
        $taxonIds = array_map(function (Taxon $taxon) {
            return $taxon->getId();
        }, $taxons);

        $in  = [];
        $taxonIdsParameters = [];

        foreach ($taxonIds as $index => $taxonId) {
            $paramName = 't_' . $taxon->getId() . '_' . $index;
            $taxonIdsParameters[$paramName] = $taxonId;
            $in[] = ':' . $paramName;
        }

        $inStr = implode(', ', $in);

        $factoryTableName = $this->em->getClassMetadata(Factory::class)->table['name'];
        $productTableName = $this->em->getClassMetadata(Product::class)->table['name'];
        $taxonTableName = $this->em->getClassMetadata(Taxon::class)->table['name'];
        $productTaxonInfo = $this->em->getClassMetadata(Product::class)
            ->associationMappings['taxons']['joinTable'];

        $productTaxonTableName = $productTaxonInfo['name'];

        $sql = "SELECT DISTINCT f.id FROM {$factoryTableName} f \n" .
            "INNER JOIN {$productTableName} p ON f.id= p.factory_id \n" .
            "INNER JOIN {$productTaxonTableName} pt ON p.id = pt.product_id \n" .
            "INNER JOIN {$taxonTableName} t ON t.id = pt.taxon_id \n" .
            "WHERE \n" .
            "t.id IN ($inStr)"
        ;

        $connection = $this->em->getConnection();
        $stm = $connection->prepare($sql);
        $stm->execute($taxonIdsParameters);

        $result = $stm->fetchAll();

        return array_map(function ($data) {
            return $data['id'];
        }, $result);
    }

    /**
     * Get all childs for taxon
     *
     * @param Taxon $taxon
     *
     * @return Taxon[]
     */
    private function getChildsForTaxon(Taxon $taxon)
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
