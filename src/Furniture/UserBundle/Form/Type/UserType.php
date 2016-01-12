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
     * {@inheritdoc}
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        parent::setDefaultOptions($resolver);

        $resolver->setDefaults([
            'mode' => null
        ]);

        $resolver->setAllowedValues([
            'mode' => [null, 'retailer']
        ]);
    }

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
            ]);

        $builder->add('retailerUserProfile', new RetailerUserProfileType());

        if ($options['mode'] == 'retailer') {
            $builder->remove('authorizationRoles');
            $builder->remove('factory');
            /*$builder->remove('plainPassword');
            
            $builder->add('plainPassword', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'The password fields must match.',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => true,
                'first_options'  => array('label' => 'frontend.new_password'),
                'second_options' => array('label' => 'frontend.repeat_password'),
            ));*/
            
            $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                $this->validateUserForRetailerProfile($event->getData(), $event->getForm());
            });
        }

        if ($options['mode'] === null) {
            $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                /** @var \Furniture\UserBundle\Entity\User $user */
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
                    'mapped'   => false,
                    'label'    => 'Frontend role',
                    'required' => false,
                    'choices'  => [
                        User::ROLE_CONTENT_USER  => 'Content user',
                        User::ROLE_FACTORY_ADMIN => 'Factory admin'
                    ],
                    'data'     => $userActiveRole
                ]);
            });
        }

        if ($options['mode'] === null) {
            $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var \Furniture\UserBundle\Entity\User $user */
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

                if ($user->getRetailerUserProfile() 
                        && $user->getRetailerUserProfile()->getRetailerProfile()) {
                    $this->validateUserForRetailerProfile($user, $event->getForm());
                }
            });
        }
    }

    /**
     * Validate user for retailer user profile
     *
     * @param User          $user
     * @param FormInterface $form
     */
    private function validateUserForRetailerProfile(User $user, FormInterface $form)
    {
        if (!$user->getRetailerUserProfile()->getRetailerMode()) {
            $form->get('retailerUserProfile')->get('retailerMode')->addError(new FormError(
                'This value should be not blank'
            ));
        }
        if (!$user->getRetailerUserProfile()->getRetailerProfile()) {
            $form->get('retailerUserProfile')->get('retailerProfile')->addError(new FormError(
                'This value should be not blank'
            ));
        }
    }
}
