<?php

namespace Furniture\UserBundle\Form\Type\Retailer;

use Furniture\RetailerBundle\Form\Type\RetailerUserProfileType;
use Sylius\Bundle\CoreBundle\Form\Type\UserType as BaseUserType;
use Furniture\UserBundle\Entity\User;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

/**
 * User form type for retailer
 */
class UserType extends BaseUserType
{
    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct(User::class, []);
    }

    /**
     * {@inheritDoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('retailerUserProfile', new RetailerUserProfileType());

        // Remove non used fields
        $builder
            ->remove('authorizationRoles');
    }
}
