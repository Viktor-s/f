<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\CommonBundle\Form\DataTransformer\CheckboxForValueTransformer;
use Furniture\ProductBundle\Entity\ProductScheme;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\Common\Collections\ArrayCollection;

class ProductSchemeType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'    => ProductScheme::class,
            'disallow_edit' => false,
        ]);

        $resolver->setRequired('parts');
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var \Furniture\ProductBundle\Entity\ProductPart[] $parts */
        $parts = $options['parts'];

        $builder->add('translations', 'a2lix_translationsForms', [
            'form_type' => new ProductSchemeTranslationType(),
            // https://github.com/a2lix/TranslationFormBundle/issues/95
            'empty_data' => function (FormInterface $form) {
                return new ArrayCollection();
            },
        ]);

        // Add event for add fields to form, because we should has a scheme data
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($parts, $builder) {
            /** @var ProductScheme $scheme */
            $scheme = $event->getData();
            $form = $event->getForm();
            $options = $builder->getOptions();

            $childFormBuilder = $builder->getFormFactory()->createNamedBuilder('productParts', 'form', null, [
                'auto_initialize' => false
            ]);

            // Add event listener for clear removes elements
            $childFormBuilder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) use ($scheme) {
                $data = $event->getData();

                foreach ($data as $key => $item) {
                    if (!$item) {
                        unset ($data[$key]);
                    }
                }

                $event->setData($data);
            }, 50);

            $childForm = $childFormBuilder->getForm();

            $index = 0;

            // Add product parts to form
            foreach ($parts as $part) {
                if ($scheme) {
                    $data = $scheme->hasProductPart($part);
                } else {
                    $data = false;
                }

                $childBuilder = $builder->getFormFactory()->createNamedBuilder($index++, 'checkbox', $data, [
                    'auto_initialize' => false,
                    'required'        => false,
                    'value'           => $part->getId(),
                    'disabled'        => $options['disallow_edit'],
                ]);

                $childBuilder->addModelTransformer(new CheckboxForValueTransformer($part));

                $childForm->add($childBuilder->getForm());
            }

            $form->add($childForm);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_scheme';
    }
}
