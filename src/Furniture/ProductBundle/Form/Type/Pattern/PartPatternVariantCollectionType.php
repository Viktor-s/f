<?php

namespace Furniture\ProductBundle\Form\Type\Pattern;

use Furniture\ProductBundle\Entity\ProductPart;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartPatternVariantCollectionType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'part_material_variants' => null,
        ]);

        $resolver->setRequired(['parts', 'variant_selection_class']);
        $resolver->setAllowedTypes('parts', \Traversable::class);
        $resolver->setAllowedTypes('variant_selection_class', 'string');
        $resolver->setAllowedTypes('part_material_variants', ['null', 'array', \Traversable::class]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Furniture\ProductBundle\Entity\ProductPart[] $parts */
        $parts = $options['parts'];
        /** @var \Furniture\ProductBundle\Entity\ProductPartMaterialVariant[] $materialVariants */
        $materialVariants = $options['part_material_variants'];

        foreach ($parts as $part) {
            $materialsForThisPart = null;

            if ($materialVariants !== null) {
                $materialsForThisPart = $this->getMaterialVariantsForProductPart($part, $materialVariants);
            }

            $builder->add($part->getId(), new PartPatternType(), [
                'variant_selection_class' => $options['variant_selection_class'],
                'part'                    => $part,
                'material_variants'       => $materialsForThisPart,
            ]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'part_pattern_variant_collection';
    }

    /**
     * Get material variants for product part
     *
     * @param ProductPart                                                  $part
     * @param \Furniture\ProductBundle\Entity\ProductPartMaterialVariant[] $materialVariants
     *
     * @return array
     */
    private function getMaterialVariantsForProductPart(ProductPart $part, $materialVariants)
    {
        $variants = [];

        foreach ($part->getProductPartMaterials() as $material) {
            foreach ($material->getVariants() as $partMaterialVariant) {
                foreach ($materialVariants as $materialVariant) {
                    if ($partMaterialVariant->getId() == $materialVariant->getId()) {
                        $variants[] = $materialVariant;
                    }
                }
            }
        }

        return $variants;
    }
}
