<?php

namespace Furniture\FrontendBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Furniture\UserBundle\Entity\User;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FactoryBundle\Entity\FactoryRetailerRelation;
use Furniture\RetailerBundle\Entity\RetailerProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormError;
use Symfony\Component\OptionsResolver\Exception\NoSuchOptionException;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Furniture\FrontendBundle\Repository\FactoryRetailerRelationRepository;
use Furniture\FrontendBundle\Repository\FactoryRepository;

class FactoryRetailerRelationType extends AbstractType
{
    
    /**
     * @var FactoryRepository
     */
    private $factoryRepository;

    /**
     * @var FactoryRetailerRelationRepository
     */
    private $factoryRelationRepository;

    /**
     * Constructor
     *
     * @param FactoryRetailerRelationRepository $factoryRelationRepository
     * @param FactoryRepository                 $factoryRepository
     */
    public function __construct(
        FactoryRetailerRelationRepository $factoryRelationRepository,
        FactoryRepository $factoryRepository
    )
    {
        $this->factoryRelationRepository = $factoryRelationRepository;
        $this->factoryRepository = $factoryRepository;
    }

        /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FactoryRetailerRelation::class,
            'factory_user' => null,
            'content_user' => null,
        ]);

        $resolver->setRequired('mode');
        $resolver->setAllowedValues('mode', ['from_factory', 'from_retailer']);
        $resolver->setAllowedTypes('factory_user', ['null', User::class]);
        $resolver->setAllowedTypes('content_user', ['null', User::class]);

        $resolver->setNormalizer('factory_user', function (OptionsResolver $resolver, $value) {
            if ($resolver->offsetGet('mode') == 'from_factory' && !$value) {
                throw new NoSuchOptionException(
                    'The option "factory_user" is required for use with mode "from_factory".'
                );
            }
        });

        $resolver->setNormalizer('content_user', function (OptionsResolver $resolver, $value) {
            if ($resolver->offsetGet('mode') == 'from_user' && !$value) {
                throw new NoSuchOptionException(
                    'The option "content_user" is required for use with mode "from_user".'
                );
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Add event listener for build form, because we can access to data and control disabled, if
        // now editing user form (mode == from_user)
        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($options) {
            /** @var FactoryRetailerRelation $relation */
            $relation = $event->getData();

            if (!$relation) {
                throw new \RuntimeException('Cannot build form without relation data.');
            }

            $form = $event->getForm();
            $disabledAccessRights = $options['mode'] == 'from_retailer' && $relation->isFactoryAccept();

            if ($options['mode'] == 'from_factory') {
                if ($relation->getId()) {
                    // Edit saved relation
                    $form->add('_retailer', 'text', [
                        'label' => 'frontend.retailer',
                        'mapped' => false,
                        'disabled' => true,
                        'data' => $relation->getRetailer()->getName()
                    ]);
                } else {
                    $form->add('retailer', 'entity', [
                        'label' => 'frontend.retailer',
                        'class' => RetailerProfile::class
                    ]);
                }
            }

            if ($options['mode'] == 'from_retailer') {
                if ($relation->getId()) {
                    // Edit saved relation
                    $form->add('_factory', 'text', [
                        'label' => 'frontend.brand',
                        'mapped' => false,
                        'disabled' => true,
                        'data' => $relation->getFactory()->getName()
                    ]);
                } else {
                    $form->add('factory', 'entity', [
                        'label' => 'frontend.factory',
                        'class' => Factory::class,
                        'query_builder' => function (EntityRepository $er) use ($relation) {
                            if( $relation->getRetailer() ){
                                return $er->createQueryBuilder('f')
                                    ->leftJoin( 'f.retailerRelations','fur', 'WITH', 'fur.retailer = :retailer')
                                    ->orWhere('fur.retailer is NULL')
                                    //If visible in front!
                                    ->andWhere('f.enabled = true')
                                    ->setParameter('retailer', $relation->getRetailer())
                                    ;
                            }else
                                return $er->createQueryBuilder('f');
                        }
                    ]);
                }
            }

            $form
                ->add('accessProducts', 'checkbox', [
                    'label' => 'frontend.products_view',
                    'disabled' => $disabledAccessRights,
                    'required' => false
                ])
                ->add('accessProductsPrices', 'checkbox', [
                    'label' => 'frontend.product_prices_view',
                    'disabled' => $disabledAccessRights,
                    'required' => false
                ])
                ->add('discount', 'number', [
                    'label' => 'frontend.discount',
                    'disabled' => $disabledAccessRights
                ])
                ->add('_submit', 'submit', [
                    'label' => 'frontend.save',
                    'attr' => [
                        'class' => 'btn btn-success col-lg-offset-2'
                    ]
                ]);
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'retailer_factory_relation';
    }
}
