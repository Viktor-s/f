<?php

namespace Furniture\CommonBundle\Form\Type;

use Sylius\Bundle\CoreBundle\Form\Type\UserType as BaseUserType;
use Symfony\Component\Form\FormBuilderInterface;

use Furniture\FactoryBundle\Entity\Factory;

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
                'empty_data'  => null,
                'data' => null,
                'required' => false
            ])
        ;
    }
}

