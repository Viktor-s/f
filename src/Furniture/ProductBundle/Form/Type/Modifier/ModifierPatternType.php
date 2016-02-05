<?php

namespace Furniture\ProductBundle\Form\Type\Modifier;

use Furniture\CommonBundle\Form\DataTransformer\ObjectToStringTransformer;
use Furniture\ProductBundle\Entity\ProductPartPatternVariantModifierSelection;
use Furniture\ProductBundle\Entity\ProductVariantsPatternModifier;
use Furniture\ProductBundle\Form\Type\Pattern\PatternType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifierPatternType extends PatternType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);

        $resolver->setDefaults([
            'data_class' => ProductVariantsPatternModifier::class,
            'variant_selection_class' => ProductPartPatternVariantModifierSelection::class
        ]);

        $resolver->setRequired('attach');
        $resolver->setAllowedValues('attach', [
            ProductVariantsPatternModifier::ATTACH_TO_PRODUCT,
            ProductVariantsPatternModifier::ATTACH_TO_PATTERN
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        if ($options['attach'] == ProductVariantsPatternModifier::ATTACH_TO_PATTERN) {
            $builder
                ->remove('scheme')
                ->add('pattern', 'text', [
                    'disabled' => true
                ]);

            $builder->get('pattern')->addModelTransformer(new ObjectToStringTransformer());
        }

        $builder
            ->add('price', 'text', [
                'label' => 'Price'
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_pattern_modifier';
    }
}
