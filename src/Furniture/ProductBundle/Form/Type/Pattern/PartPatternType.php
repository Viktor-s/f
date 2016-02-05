<?php

namespace Furniture\ProductBundle\Form\Type\Pattern;

use Furniture\ProductBundle\Entity\ProductPart;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PartPatternType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(['part', 'variant_selection_class']);
        $resolver->setAllowedTypes('part', ProductPart::class);
        $resolver->setAllowedTypes('variant_selection_class', 'string');
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ProductPart $part */
        $part = $options['part'];

        foreach ($part->getProductPartMaterials() as $material) {
            $builder->add($material->getId(), new PartMaterialPatternType(), [
                'variant_selection_class' => $options['variant_selection_class'],
                'material'                => $material,
                'part'                    => $part,
            ]);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /** @var ProductPart $part */
        $part = $options['part'];
        /** @var \Furniture\ProductBundle\Entity\ProductPartTranslation $partTranslation */
        $partTranslation = $part->translate();

        $view->vars['part'] = $part;
        $view->vars['part_label'] = $partTranslation->getLabel();
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'part_pattern';
    }
}
