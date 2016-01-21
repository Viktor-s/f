<?php

namespace Furniture\UserBundle\Form\Type\Retailer;

use Furniture\UserBundle\Form\Type\CustomerType as BaseCustomerType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * Customer type for retailer
 */
class CustomerType extends BaseCustomerType
{
    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults([
            'validation_groups' => function (Form $form) {
                /** @var \Furniture\UserBundle\Entity\Customer $customer */
                $customer = $form->getData();

                if ($customer->getId()) {
                    return ['Update', 'RetailerProfileUpdate'];
                } else {
                    return ['Create', 'RetailerProfileCreate'];
                }
            }
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->remove('groups');
    }

    /**
     * {@inheritDoc}
     */
    protected function getUserFormType()
    {
        return new UserType();
    }
}
