<?php

namespace Furniture\CommonBundle\Form\Type;

use Furniture\CommonBundle\Entity\User;
use Sylius\Bundle\CoreBundle\Form\Type\UserType as BaseUserType;
use Symfony\Component\Form\FormBuilderInterface;

use Furniture\FactoryBundle\Entity\Factory;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Furniture\RetailerBundle\Form\Type\RetailerUserProfileType;

class UserType extends BaseUserType 
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('factory', 'entity', [
                'class' => Factory::class,
                'multiple' => false,
                'required' => false
            ])
            ->add('retailerUserProfile', new RetailerUserProfileType());

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
            /** @var User $user */
            $user = $event->getData();

            $userActiveRole = null;

            if ($user) {
                if ($user->isContentUser()) {
                    $userActiveRole = User::ROLE_CONTENT_USER;
                } else if ($user->isFactoryAdmin()) {
                    $userActiveRole = User::ROLE_FACTORY_ADMIN;
                }
            }

            $event->getForm()->add('role', 'choice', [
                'mapped' => false,
                'label' => 'Frontend role',
                'required' => false,
                'choices' => [
                    User::ROLE_CONTENT_USER => 'Content user',
                    User::ROLE_FACTORY_ADMIN => 'Factory admin'
                ],
                'data' => $userActiveRole
            ]);
        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var User $user */
            $user = $event->getData();
            $role = $event->getForm()->get('role')->getData();

            // Remove roles
            $user->removeRole(User::ROLE_CONTENT_USER);
            $user->removeRole(User::ROLE_FACTORY_ADMIN);

            if ($role == User::ROLE_CONTENT_USER) {
                $user->addRole(User::ROLE_CONTENT_USER);
            } else if ($role == User::ROLE_FACTORY_ADMIN) {
                $user->addRole(User::ROLE_FACTORY_ADMIN);
            }

            if ($user->getRetailerUserProfile()) {
                if (!$user->getRetailerUserProfile()->getRetailerMode()) {
                    $event->getForm()->get('retailerUserProfile.retailerMode')->addError(new FormError(
                        'This value should be not blank'
                    ));
                }
                if(!$user->getRetailerUserProfile()->getRetailerProfile()){
                    $event->getForm()->get('retailerUserProfile.retailerMode')->addError(new FormError(
                        'This value should be not blank'
                    ));
                }
            }
        });
    }
}
