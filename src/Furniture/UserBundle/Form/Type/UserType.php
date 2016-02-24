<?php

namespace Furniture\UserBundle\Form\Type;

use Furniture\UserBundle\Entity\User;
use Sylius\Bundle\CoreBundle\Form\Type\UserType as BaseUserType;
use Symfony\Component\Form\FormBuilderInterface;

use Furniture\FactoryBundle\Entity\Factory;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Furniture\RetailerBundle\Form\Type\RetailerUserProfileType;
use Symfony\Component\Form\FormInterface;
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
     * {@inheritdoc}
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

        $builder->add('needResetPassword');
        $builder
            ->add('factory', 'entity', [
                'class'    => Factory::class,
                'multiple' => false,
                'required' => false,
            ]);

        $builder->add('retailerUserProfile', new RetailerUserProfileType());

        // Add event listener for add role field
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var \Furniture\UserBundle\Entity\User $user */
            $user = $event->getData();

            $userActiveRole = null;

            if ($user) {
                if ($user->isContentUser()) {
                    $userActiveRole = User::ROLE_CONTENT_USER;
                } elseif ($user->isFactoryAdmin()) {
                    $userActiveRole = User::ROLE_FACTORY_ADMIN;
                }
            }

            $event->getForm()->add('role', 'choice', [
                'mapped'   => false,
                'label'    => 'Frontend role',
                'required' => false,
                'choices'  => [
                    User::ROLE_CONTENT_USER  => 'Content user',
                    User::ROLE_FACTORY_ADMIN => 'Factory admin',
                ],
                'data'     => $userActiveRole,
            ]);
        });

        // Add event listener for validate roles
        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var \Furniture\UserBundle\Entity\User $user */
            $user = $event->getData();

            $role = $event->getForm()->get('role')->getData();

            // Remove roles
            $user->removeRole(User::ROLE_CONTENT_USER);
            $user->removeRole(User::ROLE_FACTORY_ADMIN);

            if ($role == User::ROLE_CONTENT_USER) {
                $user->addRole(User::ROLE_CONTENT_USER);
            } elseif ($role == User::ROLE_FACTORY_ADMIN) {
                $user->addRole(User::ROLE_FACTORY_ADMIN);
            }
        });
    }
}
