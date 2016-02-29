<?php

namespace Furniture\FactoryBundle\Form\Type;

use Furniture\FactoryBundle\Entity\FactoryTranslation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactoryTranslationType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FactoryTranslation::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('description', 'textarea', ['attr' => ['class' => 'ckeditor']])
            ->add('shortDescription', 'textarea')
            ->add('address')
            ->add('workInfoContent', 'textarea', [
                'label' => 'frontend.factory_side.work_info.terms_payment',
                'attr' => ['class' => 'ckeditor']
            ])
            ->add('collectionContent', 'textarea',[
                'label' => 'Collections',
                'attr'  => ['class' => 'ckeditor'],
            ])
            ->add('bankDetails', 'textarea', [
                'label' => 'frontend.factory_side.work_info.bank_details',
                'attr' => ['class' => 'ckeditor']
            ])
            ->add('productTime', 'textarea', [
                'label' => 'frontend.factory_side.work_info.production_time',
                'attr' => ['class' => 'ckeditor']
            ])
            ->add('deliveryAndPackaging', 'textarea', [
                'label' => 'frontend.factory_side.work_info.delivery_packaging',
                'attr' => ['class' => 'ckeditor']
            ])
            ->add('vacations', 'textarea', [
                'label' => 'frontend.factory_side.work_info.vacations',
                'attr' => ['class' => 'ckeditor']
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'furniture_factorybundle_factory_translation';
    }
}
