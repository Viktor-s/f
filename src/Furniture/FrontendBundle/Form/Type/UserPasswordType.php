<?php

namespace Furniture\FrontendBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Furniture\CommonBundle\Entity\User;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserPasswordType extends AbstractType
{
    
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class
        ]);
    }
    
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('plainPassword', 'repeated', array(
            'type' => 'password',
            'invalid_message' => 'The password fields must match.',
            'options' => array('attr' => array('class' => 'password-field')),
            'required' => true,
            'first_options'  => array('label' => 'frontend.new_password'),
            'second_options' => array('label' => 'frontend.repeat_password'),
        ))
        ;
    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'frontend_user_password_type';
    }
    
}

