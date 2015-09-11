<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\ProductBundle\Entity\ProductExtension;
use Furniture\ProductBundle\Entity\ProductExtensionOptionValue;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form for product extension
 */
class ProductExtensionType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductExtension::class
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', 'text', [
                'label' => 'product_extension.form.name'
            ])
            ->add('translations', 'a2lix_translationsForms', [
                'form_type' => new ProductExtensionTranslationType()
            ])
            ->add('optionValues', 'collection', [
                'type' => new ProductExtensionOptionValueType(),
                'allow_add' => true,
                'allow_delete' => true
            ]);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            /** @var ProductExtension $extension */
            $extension = $event->getData();

            $extension->getOptionValues()->forAll(function ($key, ProductExtensionOptionValue $optionValue) use ($extension) {
                $optionValue->setExtension($extension);

                return true;
            });
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_extension';
    }
}
