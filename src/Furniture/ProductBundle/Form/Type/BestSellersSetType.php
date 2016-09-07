<?php

namespace Furniture\ProductBundle\Form\Type;


use Furniture\FactoryBundle\Entity\Factory;
use Furniture\ProductBundle\Entity\BestSellersSet;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BestSellersSetType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'    => BestSellersSet::class,
            'cascade_validation' => true,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'label' => 'product_best_sellers.form.name'
            ])
            ->add('active', 'checkbox', [
                'label' => 'product_best_sellers.form.active'
            ])
            ->add(
                'bestSellers',
                'collection',
                [
                    'type'         => new BestSellersType(),
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                    'label'        => 'product_best_sellers.form.best_sellers',
                ]
            );
//            ->add(
//                'productFactory',
//                'entity',
//                [
//                    'class'    => Factory::class,
//                    'multiple' => false,
//                    'property' => 'name',
//                    'mapped'   => false,
//                    'required' => false,
//                    'attr'     => [
//                        'class'                => 'autocomplete-extra-params',
//                        'data-extra-parm-name' => 'factory',
//                        'data-parent-widget'   => 'product',
//                    ],
////                    'data'     => $product->getId() ? $product->getFactory() : null,
//                ]
//            );
    }
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_best_sellers_set';
    }
}