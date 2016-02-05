<?php

namespace Furniture\ProductBundle\Form\Type\Pattern;

use Furniture\ProductBundle\Entity\ProductPartMaterial;
use Furniture\ProductBundle\Entity\ProductPart;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartMaterialPatternType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['material', 'part', 'variant_selection_class']);
        $resolver->setAllowedTypes('material', ProductPartMaterial::class);
        $resolver->setAllowedTypes('part', ProductPart::class);
        $resolver->setAllowedTypes('variant_selection_class', 'string');
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ProductPartMaterial $material */
        $material = $options['material'];

        foreach ($material->getVariants() as $variant) {
            $builder->add($variant->getId(), new PartPatternVariantSelectionType(), [
                'data_class' => $options['variant_selection_class'],
                'label'      => $variant->getName(),
                'variant'    => $variant,
                'part'       => $options['part'],
            ]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /** @var ProductPartMaterial $material */
        $material = $options['material'];

        $view->vars['material'] = $material;
        $view->vars['material_label'] = $material->getName();
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'part_material_pattern';
    }
}
