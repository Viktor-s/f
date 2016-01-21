<?php

namespace Furniture\UserBundle\Form\Type;

use Sylius\Bundle\UserBundle\Form\Type\CustomerType as BaseCustomerType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Furniture\UserBundle\Entity\Customer;

/**
 * A base customer type. Includes all info about user and customer and retailer profile
 */
class CustomerType extends BaseCustomerType
{
    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct(Customer::class, []);
    }

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
                    return ['Update'];
                } else {
                    return ['Create'];
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

        // Replace email field for change label
        $builder
            ->remove('email')
            ->add('email', 'email', [
                'label' => 'Email (use for login)'
            ]);

        // Remove non use fields
        $builder->remove('birthday');

        // Replace user form
        $builder->add('user', $this->getUserFormType());
    }

    /**
     * Get a user form type
     *
     * @return UserType
     */
    protected function getUserFormType()
    {
        return new UserType();
    }
}
