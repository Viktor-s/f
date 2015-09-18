<?php

namespace Furniture\FactoryBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class FactoryUserRelationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $read_only = false;
        $entity = $builder->getForm()->getData();
        if( $entity->getId() ){
            $read_only = true;
        }
        
        $builder
            ->add('user', 'entity', [
                'class' => 'Furniture\CommonBundle\Entity\User',
                'multiple'  => false,
                'expanded'  => false,
                'read_only' => $read_only,
                'query_builder' => function(EntityRepository $r) use ($options, $entity) {
                    return $r->createQueryBuilder('u')
                            ->leftJoin('u.authorizationRoles', 'ar')
                            ->leftJoin('u.factoryRelations', 'fr')
                            ->where('ar.code IN (:codes) AND (fr.factory != :factory_id OR fr.factory IS NULL )' )
                              ->setParameter('codes', $options['content_access_user_roles'])
                              ->setParameter('factory_id', $entity->getFactory()->getId())
                            
                        ;
                }
            ])
            ->add('isActive', 'checkbox', ['label' => 'Activate rule'])
            ->add('accessProducts', 'checkbox', ['label' => 'Can see product list'])
            ->add('accessProductsPrices', 'checkbox', ['label' => 'Can work with prices'])
            ->add('discount', 'integer', ['label' => 'Set price discount'])
            ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Furniture\FactoryBundle\Entity\FactoryUserRelation',
            'content_access_user_roles' => []
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'furniture_factorybundle_factory_user_relation';
    }
}
