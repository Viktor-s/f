<?php

namespace Furniture\ProductBundle\Form\Type\PdpIntellectual;

use Furniture\CommonBundle\Form\DataTransformer\ObjectToStringTransformer;
use Furniture\ProductBundle\Entity\PdpIntellectualRoot;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PdpIntellectualRootType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PdpIntellectualRoot::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('product', 'text', [
                'label' => 'Product',
                'disabled' => true
            ])
            ->add('name', 'text', [
                'label' => 'Name'
            ])
            ->add('graphJson', 'textarea', [
                'read_only' => true,
            ]);

        $builder->get('graphJson')
            ->addModelTransformer(new CallbackTransformer(
                function ($array) {
                    return !empty($array) ? json_encode($array) : '';
                },
                function ($json) {
                    return json_decode($json, true);
                }
            ));

        $builder->get('product')->addModelTransformer(new ObjectToStringTransformer());
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'pdp_intellectual_root';
    }
}
