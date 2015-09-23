<?php

namespace Furniture\FactoryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class FactoryType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', 'a2lix_translationsForms', [
                'form_type' => new FactoryTranslationType('Furniture\FactoryBundle\Entity\FactoryTranslation')
            ])
            ->add('name', 'text', [
                'required' => true
            ])
            ->add('images', 'collection', array(
                'type'         => new \Sylius\Bundle\CoreBundle\Form\Type\ImageType('Furniture\FactoryBundle\Entity\FactoryImage'),
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
            ))
            ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Furniture\FactoryBundle\Entity\Factory'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'furniture_factorybundle_factory';
    }
}
