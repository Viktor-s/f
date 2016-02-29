<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\ProductBundle\Entity\ProductPartMaterial;
use Furniture\ProductBundle\Entity\ProductPartMaterialOptionValue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form for product extension
 */
class ProductPartMaterialType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'         => ProductPartMaterial::class,
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
                'label' => 'product_part_material.form.name'
            ])
            ->add('factory', 'entity', [
                'class' => Factory::class,
                'required' => false,
            ])
            ->add('translations', 'a2lix_translationsForms', [
                'form_type' => new ProductPartMaterialTranslationType(),
            ])
            ->add('optionValues', 'collection', [
                'type' => new ProductPartMaterialOptionValueType(),
                'allow_add' => true,
                'allow_delete' => true,
                'attr' => [
                    'data-remove-confirm' => 'Are you sure you want to delete option values item?'
                ]
            ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var ProductPartMaterial $extension */
            $extension = $event->getData();

            $extension->getOptionValues()->forAll(function ($key, ProductPartMaterialOptionValue $optionValue) use ($extension) {
                $optionValue->setMaterial($extension);

                return true;
            });
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_part_material';
    }
}
