<?php

namespace Furniture\FactoryBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FactoryBundle\Entity\FactoryRetailerRelation;
use Furniture\RetailerBundle\Entity\RetailerProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class FactoryRetailerRelationFilterType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @param $em
     */
    public function __construct($em) {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('retailer', 'entity', [
                'class' => RetailerProfile::class,
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Retailer',
                ],
                'query_builder' => function(EntityRepository $er ) {
                    return $er
                        ->createQueryBuilder('rp')
                        ->join(FactoryRetailerRelation::class, 'fur', 'WITH', 'fur.retailer = rp.id');
                },
                'data' => !empty($options['data']['retailer'])
                    ? $this->em->getReference(RetailerProfile::class, $options['data']['retailer'])
                    : null,
            ])
            ->add('factory', 'entity', [
                'class' => Factory::class,
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Factory',
                ],
                'query_builder' => function(EntityRepository $er ) {
                    return $er
                        ->createQueryBuilder('f')
                        ->join(FactoryRetailerRelation::class, 'fur', 'WITH', 'fur.factory = f.id');
                },
                'data' => !empty($options['data']['factory'])
                    ? $this->em->getReference(Factory::class, $options['data']['factory'])
                    : null,
            ])
            ->add('status', 'choice', [
                'choices' => [
                    'all' => 'All',
                    'approve' => 'Approved',
                    'decline' => 'Declined',
                    'wait' => 'Waiting',
                ],
                'label' => false,
                'attr' => [
                    'placeholder' => 'Status',
                ]
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'factory_retailer_relation_filter';
    }
}
