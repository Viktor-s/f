<?php

namespace Furniture\ProductBundle\Form\Type\ProductPattern;

use Doctrine\ORM\EntityRepository;
use Furniture\CommonBundle\Form\DataTransformer\ObjectToStringTransformer;
use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Entity\ProductScheme;
use Furniture\ProductBundle\Entity\ProductVariantsPattern;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductPatternWithoutSchemaType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'validation_groups' => ['WithoutSchema'],
            'data_class' => ProductVariantsPattern::class
        ]);

        $resolver->setRequired('product');
        $resolver->setAllowedTypes('product', Product::class);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product', 'text', [
                'disabled' => true,
                'label'    => 'Product',
            ])
            ->add('scheme', 'entity', [
                'class'         => ProductScheme::class,
                'required'      => false,
                'label'         => 'Scheme',
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('s')
                        ->innerJoin('s.product', 'p')
                        ->andWhere('p.id = :product')
                        ->setParameter('product', $options['product']);
                },
            ]);

        $builder->get('product')->addModelTransformer(new ObjectToStringTransformer());
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_pattern_without_schema';
    }
}
