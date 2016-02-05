<?php

namespace Furniture\ProductBundle\Form\Type\Modifier;

use Doctrine\ORM\EntityRepository;
use Furniture\CommonBundle\Form\DataTransformer\ObjectToStringTransformer;
use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Entity\ProductScheme;
use Furniture\ProductBundle\Entity\ProductVariantsPattern;
use Furniture\ProductBundle\Entity\ProductVariantsPatternModifier;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ModifierWithoutPatternAndProductType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductVariantsPatternModifier::class,
            'validation_groups' => function (Form $form) {
                /** @var ProductVariantsPatternModifier $data */
                $data = $form->getData();

                if ($data->isAttachedToProduct()) {
                    return ['ModifierWithProduct'];

                } else if ($data->isAttachedToPattern()) {
                    return ['ModifierWithPattern'];

                }

                return ['Default'];
            }
        ]);

        $resolver->setRequired('product');
        $resolver->setAllowedTypes('product', Product::class);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Furniture\ProductBundle\Entity\Product $product */
        $product = $options['product'];

        if ($product->isSchematicProductType()) {
            $builder->add('scheme', 'entity', [
                'required'      => false,
                'class'         => ProductScheme::class,
                'query_builder' => function (EntityRepository $er) use ($product) {
                    return $er->createQueryBuilder('s')
                        ->andWhere('s.product = :product')
                        ->setParameter('product', $product);
                },
            ]);
        }

        $builder
            ->add('attach', 'choice', [
                'label'   => 'Attach to',
                'choices' => [
                    ProductVariantsPatternModifier::ATTACH_TO_PRODUCT => 'Product',
                    ProductVariantsPatternModifier::ATTACH_TO_PATTERN => 'Pattern',
                ],
            ])
            ->add('product', 'text', [
                'label'    => 'Product',
                'disabled' => true
            ])
            ->add('pattern', 'entity', [
                'required'      => false,
                'class'         => ProductVariantsPattern::class,
                'query_builder' => function (EntityRepository $er) use ($product) {
                    return $er->createQueryBuilder('p')
                        ->andWhere('p.product = :product')
                        ->setParameter('product', $product);
                },
            ]);

        $builder->get('product')->addModelTransformer(new ObjectToStringTransformer());

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var ProductVariantsPatternModifier $modifier */
            $modifier = $event->getData();

            if ($modifier->isAttachedToPattern()) {
                $modifier->setScheme(null);
            } else if ($modifier->isAttachedToProduct()) {
                $modifier->setPattern(null);
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'modifier_without_pattern_and_product';
    }
}
