<?php

namespace Furniture\SpecificationBundle\Form\Type;

use Furniture\SpecificationBundle\Entity\Buyer;
use Furniture\SpecificationBundle\Entity\BuyerImage;
use Sylius\Bundle\CoreBundle\Form\Type\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BuyerType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Buyer::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('firstName', 'text', [
                'label' => 'specification.buyer.form.first_name'
            ])
            ->add('secondName', 'text', [
                'label' => 'specification.buyer.form.second_name'
            ])
            ->add('image', new ImageType(BuyerImage::class))
            ->add('phone', 'text', [
                'label' => 'specification.buyer.form.phone',
                'required' => false
            ])
            ->add('email', 'text', [
                'label' => 'specification.buyer.form.email',
                'required' => false
            ])
            ->add('address', 'text', [
                'label' => 'specification.buyer.form.address',
                'required' => false
            ])
            ->add('sale', 'number', [
                'label' => 'specification.buyer.form.sale'
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'specification_buyer';
    }
}
