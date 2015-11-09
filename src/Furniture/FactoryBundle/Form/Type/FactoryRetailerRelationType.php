<?php

namespace Furniture\FactoryBundle\Form\Type;

use Furniture\FactoryBundle\Entity\FactoryRetailerRelation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class FactoryRetailerRelationType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => FactoryRetailerRelation::class
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $readOnly = false;
        $entity = $builder->getForm()->getData();

        if ($entity->getId()) {
            $readOnly = true;
        }
        
        $builder
            ->add('retailer', 'entity', [
                'class' => 'Furniture\RetailerBundle\Entity\RetailerProfile',
                'multiple'  => false,
                'expanded'  => false,
                'read_only' => $readOnly,
                'query_builder' => function(EntityRepository $r) {
                    return $r->createQueryBuilder('rp');
                }
            ])
            ->add('isActive', 'checkbox', ['label' => 'Activate rule'])
            ->add('accessProducts', 'checkbox', ['label' => 'Can see product list'])
            ->add('accessProductsPrices', 'checkbox', ['label' => 'Can work with prices'])
            ->add('discount', 'integer', ['label' => 'Set price discount']);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'factory_retailer_relation';
    }
}
