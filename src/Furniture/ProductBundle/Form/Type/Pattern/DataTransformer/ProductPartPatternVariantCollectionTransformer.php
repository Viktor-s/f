<?php

namespace Furniture\ProductBundle\Form\Type\Pattern\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

/**
 * We use custom form for grouping product part pattern variant selection, and this model transformer convert
 * doctrine collection to necessary array data before create form, and convert from array to collection object
 * before submit form.
 *
 * The data should be a:
 *      {ProductPart.id} -> Collection
 *      |
 *      ->  {ProductPartMaterial.id} -> Collection
 *          |
 *          ->  {ProductPartVariantSelection} -> Custom type (ProductPartPatternVariantSelectionType)
 *              |
 *              ->  {productPartMaterialVariant} -> Checkbox
 */
class ProductPartPatternVariantCollectionTransformer implements DataTransformerInterface
{
    /**
     * @var \Furniture\ProductBundle\Entity\ProductPart[]
     */
    private $parts;

    /**
     * Construct
     *
     * @param \Furniture\ProductBundle\Entity\ProductPart[] $parts
     */
    public function __construct($parts)
    {
        $this->parts = $parts;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        $searchVariantSelection = function ($id, $partId, $materialId) use ($value) {
            /** @var \Furniture\ProductBundle\Entity\ProductPartPatternVariantSelection $item */
            foreach ($value as $item) {
                $materialVariant = $item->getProductPartMaterialVariant();
                $material = $materialVariant->getMaterial();
                $productPart = $item->getProductPart();

                if (
                    $materialVariant->getId() == $id &&
                    $productPart->getId() == $partId &&
                    $material->getId() == $materialId
                ) {
                    return $item;
                }
            }

            return null;
        };

        $transformedData = [];

        foreach ($this->parts as $part) {
            $partId = $part->getId();

            if (!isset($transformedData[$partId])) {
                $transformedData[$partId] = [];
            }

            $materialData = &$transformedData[$partId];

            foreach ($part->getProductPartMaterials() as $material) {
                $materialId = $material->getId();

                if (!isset($materialData[$materialId])) {
                    $materialData[$materialId] = [];
                }

                foreach ($material->getVariants() as $variant) {
                    $selectionVariant = $searchVariantSelection($variant->getId(), $partId, $materialId);

                    $materialData[$materialId][$variant->getId()] = $selectionVariant;
                }
            }
        }

        return $transformedData;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        $variantSelections = new ArrayCollection();

        foreach ($value as $materials) {
            foreach ($materials as $variants) {
                foreach ($variants as $selection) {
                    if ($selection) {
                        $variantSelections[] = $selection;
                    }
                }
            }
        }

        return $variantSelections;
    }
}
