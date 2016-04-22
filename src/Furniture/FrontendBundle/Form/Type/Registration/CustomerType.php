<?php

namespace Furniture\FrontendBundle\Form\Type\Registration;

use Furniture\UserBundle\Entity\Customer;
use Sylius\Bundle\UserBundle\Form\Type\CustomerType as BaseCustomerType;
use Furniture\FrontendBundle\Form\Type\Registration\UserType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints as Asserts;

/**
 * Customer type for retailer
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
            'validation_groups' => ['RetailerProfileCreate'],
            'cascade_validation' => true,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('userType', 'choice', [
            'choices'     => [
                0 => 'Retailer',
                1 => 'Interior designer',
                2 => 'Simple client',
                3 => 'Producer',
            ],
            'label'       => false,
            'expanded'    => true,
            'constraints' => [
                new Asserts\NotBlank(),
            ],
            'mapped'      => false,
        ]);

        // Replace email field for change label
        $builder
            ->remove('email')
            ->add('email', 'email', [
                'label' => 'Email (use for login)'
            ]);
        $builder->get('lastName')->setRequired(false);
        // Remove non use fields
        $builder
            ->remove('birthday')
            ->remove('groups')
            ->remove('gender');
        $builder->add('user', $this->getUserFormType(), ['label' => false]);
    }

    /**
     * {@inheritDoc}
     */
    protected function getUserFormType()
    {
        return new UserType();
    }
}
