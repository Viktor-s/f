<?php

namespace Furniture\ProductBundle\Form\Type\ProductPattern;

use Furniture\CommonBundle\Form\DataTransformer\CheckboxForValueTransformer;
use Furniture\ProductBundle\Entity\ProductPart;
use Furniture\ProductBundle\Entity\ProductPartMaterialVariant;
use Furniture\ProductBundle\Entity\ProductPartPatternVariantSelection;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPartPatternVariantSelectionType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductPartPatternVariantSelection::class
        ]);

        $resolver->setRequired(['variant', 'part']);
        $resolver->setAllowedTypes('variant', ProductPartMaterialVariant::class);
        $resolver->setAllowedTypes('part', ProductPart::class);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ProductPartMaterialVariant $variant */
        $variant = $options['variant'];
        /** @var ProductPart $part */
        $part = $options['part'];

        $builder
            ->add('productPartMaterialVariant', 'checkbox', [
                'label' => $variant->getName()
            ]);

        $builder->get('productPartMaterialVariant')->addModelTransformer(new CheckboxForValueTransformer($variant));

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($variant, $part) {
            /** @var ProductPartPatternVariantSelection $data */
            $data = $event->getData();

            if (!$data->getProductPartMaterialVariant()) {
                // The variant does not checked. Should remove data.
                $event->setData(null);

                // Attention: we must set variant, because doctrine UnitOfWork try update this entity
                // with null field, and as next we has a critical db errors (not null constraint)
                $data->setProductPartMaterialVariant($variant);
            } else {
                $data->setProductPart($part);
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_part_pattern_variant_selection';
    }
}
