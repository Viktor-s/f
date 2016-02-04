<?php

namespace Furniture\ProductBundle\Form\Type\ProductPattern;

use Furniture\ProductBundle\Entity\ProductPart;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPartPatternType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('part');
        $resolver->setAllowedTypes('part', ProductPart::class);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ProductPart $part */
        $part = $options['part'];

        foreach ($part->getProductPartMaterials() as $material) {
            $builder->add($material->getId(), new ProductPartMaterialPatternType(), [
                'material' => $material,
                'part'     => $part,
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
        return 'product_part_pattern';
    }
}
