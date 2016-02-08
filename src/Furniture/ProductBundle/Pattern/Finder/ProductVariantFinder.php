<?php

namespace Furniture\ProductBundle\Pattern\Finder;

use Doctrine\DBAL\Query\QueryBuilder;
use Doctrine\ORM\EntityManagerInterface;
use Furniture\ProductBundle\Entity\ProductVariant;
use Furniture\ProductBundle\Pattern\Exception\ManyProductVariantsByParametersException;
use Furniture\ProductBundle\Pattern\ProductVariantParameters;

/**
 * Service for find SKU (ProductVariant by parameters)
 */
class ProductVariantFinder
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
     * Find SKU (Product variant) by pattern and parameter selections
     *
     * @param ProductVariantParameters $variantParameters
     *
     * @returns ProductVariant|null Returns the SKU if exist, or null if not exist
     *
     * @throws ManyProductVariantsByParametersException
     */
    public function find(ProductVariantParameters $variantParameters)
    {
        $qb = new QueryBuilder($this->em->getConnection());

        $qb
            ->select('DISTINCT pv.id AS id')
            ->from('product_variant', 'pv');

        $materialVariantsSelections = $variantParameters->getMaterialVariantSelections();

        if (count($materialVariantsSelections)) {
            // Create inner SQL for control material variants
            $materialVariantsQb = new QueryBuilder($this->em->getConnection());
            $materialVariantsQb
                ->select('COUNT(ppmv.id) AS count_material_variants_for_variant')
                ->from('product_part_material_variant', 'ppmv')
                ->innerJoin('ppmv', 'furniture_product_part_variant_selection', 'ppvs', 'ppvs.product_part_material_variant_id = ppmv.id')
                ->andWhere('ppvs.product_variant_id = pv.id');

            $index = 0;
            $materialVariantsOrX = $materialVariantsQb->expr()->orX();
            foreach ($materialVariantsSelections as $materialVariantSelection) {
                $materialVariant = $materialVariantSelection->getMaterialVariant();

                $key = sprintf('ppmv_id_' . ($index++));
                $materialVariantsOrX->add('ppmv.id = :' . $key);
                $qb->setParameter($key, $materialVariant->getId());
            }

            $materialVariantsQb->andWhere($materialVariantsOrX);

            $materialVariantsSql = $materialVariantsQb->getSQL();

            $qb
                ->andWhere('(' . $materialVariantsSql . ') = :count_material_variants')
                ->setParameter('count_material_variants', count($materialVariantsSelections));
        }

        if ($variantParameters->getProductScheme()) {
            $qb
                ->andWhere('pv.product_scheme_id = :product_scheme')
                ->setParameter('product_scheme', $variantParameters->getProductScheme()->getId());
        }

        // We set a two results for next control many sku by this parameters
        $qb->setMaxResults(2);

        $stmt = $qb->execute();

        $result = $stmt->fetchAll();
        $variantIds = [];

        foreach ($result as $item) {
            $variantIds[] = $item['id'];
        }

        if (count($variantIds) > 1) {
            throw new ManyProductVariantsByParametersException();
        }

        if (!count($variantIds)) {
            return null;
        }

        $id = $variantIds[0];

        $variant = $this->em->find(ProductVariant::class, $id);

        return $variant;
    }
}
