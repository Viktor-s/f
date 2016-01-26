<?php

namespace Furniture\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;

class UserResetPasswordRequestType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', 'email', [
                'label' => 'Email',
                'constraints' => [
                    new Assert\Email()
                ]
            ])
            ->add('submit', 'submit', [
                'label' => 'Reset password'
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'reset_password_request';
    }
}
