<?php

namespace Furniture\FrontendBundle\Form\Type;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FactoryBundle\Entity\RetailerFactoryRate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class UserFactoryRateType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => RetailerFactoryRate::class,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $factoryRate = $builder->getData();
        
        $builder
            ->add('factory', 'entity', [
                'class' => Factory::class,
                'label' => 'frontend.factory',
                'query_builder' => function (EntityRepository $er) use ($factoryRate) {
                    if($factoryRate->getRetailer())
                        return $er->createQueryBuilder('f')
                            ->leftJoin(
                                    RetailerFactoryRate::class, 'rfr',
                                    'with', 'rfr.factory = f.id AND rfr.retailer = :retailer')
                            ->andWhere('rfr.retailer is NULL')
                            ->setParameter('retailer', $factoryRate->getRetailer())
                        ;
                    else
                        return $er->createQueryBuilder('f');
                }
            ])
            ->add('coefficient', 'number', [
                'label' => 'frontend.coefficient'
            ])
            ->add('dumping', 'number', [
                'label' => 'frontend.dumping',
            ])
            ->add('_submit', 'submit', [
                'label' => 'frontend.save',
                'attr' => [
                    'class' => 'btn btn-primary'
                ]
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'user_factory_rate';
    }
}
