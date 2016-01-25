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
            ->add('email', 'email', [
                'label' => 'Email',
                'constraints' => [
                    new Assert\Email()
                ]
            ]);
    }
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'security_reset_password';
    }
}
