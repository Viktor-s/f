<?php

namespace Furniture\FrontendBundle\Form\Type\Registration;

use Furniture\UserBundle\Entity\User;
use Sylius\Bundle\CoreBundle\Form\Type\UserType as BaseUserType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Furniture\FrontendBundle\Form\Type\Registration\RetailerUserProfileType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\ResolvedFormType;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

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
            ->remove('role')
            ->remove('authorizationRoles')
            ->remove('enabled')
            ->remove('plainPassword');

        $builder->add('retailerUserProfile', new RetailerUserProfileType(), ['label' => false]);

        // Add event listener for validate roles
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var \Furniture\UserBundle\Entity\User $user */
            $user = $event->getData();

            // Remove roles
            $user->removeRole(User::ROLE_CONTENT_USER);
            $user->removeRole(User::ROLE_FACTORY_ADMIN);
            $user->addRole(User::ROLE_CONTENT_USER);
        });
    }
}
