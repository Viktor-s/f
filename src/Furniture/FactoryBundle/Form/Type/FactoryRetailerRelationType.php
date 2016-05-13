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
            'data_class' => FactoryRetailerRelation::class,
            'admin_side_access' => false,
        ));
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var FactoryRetailerRelation $entity */
        $entity = $builder->getForm()->getData();
        $readOnly = $entity->getId() ? true : false;

        if ($options['admin_side_access'] && $readOnly) {
            $builder
                ->add('retailer', 'entity_hidden', [
                    'class' => get_class($entity->getRetailer())
                ])
                ->add('factoryAccept', 'checkbox', [
                    'label' => 'Accepted by factory'
                ])
                ->add(
                    'retailerAccept',
                    ($entity->isRetailerAccept() ? 'hidden' : 'checkbox'),
                    [
                        'label' => 'Accepted by retailer',
                    ]
                );
        }
        else {
            $builder->add('retailer', 'entity', [
                'class' => 'Furniture\RetailerBundle\Entity\RetailerProfile',
                'multiple'  => false,
                'expanded'  => false,
                'read_only' => $readOnly,
                'query_builder' => function(EntityRepository $r) {
                    return $r->createQueryBuilder('rp');
                }
            ]);
        }

        $builder->add('active', 'checkbox', ['label' => 'Activate rule'])
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
