<?php

namespace Furniture\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserResetPasswordType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('password', 'repeated', [
                'label' => 'New password',
                'type' => 'password',
                'invalid_message' => 'The password fields must match.',
                'first_options' => [
                    'label' => 'Password',
                    'constraints' => [
                        new Assert\NotBlank()
                    ]
                ],
                'second_options' => [
                    'label' => 'Please confirm password',
                    'constraints' => [
                        new Assert\NotBlank()
                    ]
                ]
            ])
            ->add('submit', 'submit', [
                'label' => 'Change password'
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'reset_password';
    }
}
