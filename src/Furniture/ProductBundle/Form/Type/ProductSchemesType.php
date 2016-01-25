<?php

namespace Furniture\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSchemesType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'type'         => new ProductSchemeType(),
            'allow_add'    => true,
            'allow_delete' => true,
            'parts'        => []
        ]);

        $resolver->setRequired('parts');

        $resolver->setNormalizer('options', function (OptionsResolver $resolver) {
            return [
                'parts' => $resolver['parts']
            ];
        });

        $resolver->setNormalizer('mapped', function (OptionsResolver $resolver) {
            return count($resolver['parts']) >= 2;
        });
    }

    /**
     * {@inheritdoc}
     */
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        /** @var \Furniture\ProductBundle\Entity\ProductPart[] $parts */
        $parts = $options['parts'];
        $labels = [];

        if (count($parts) < 2) {
            $view->vars['part_error'] = 'You need to create at least two product parts at this product to create|edit schemas.';

            return;
        }

        foreach ($parts as $part) {
            /** @var \Furniture\ProductBundle\Entity\ProductPartTranslation $translate */
            $translate = $part->translate();
            $labels[] = $translate->getLabel();
        }

        $view->vars['part_labels'] = $labels;
        $view->vars['part_count'] = count($labels);
    }

    /**
     * {@inheritDoc}
     */
    public function getParent()
    {
        return 'collection';
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_schemes';
    }
}
