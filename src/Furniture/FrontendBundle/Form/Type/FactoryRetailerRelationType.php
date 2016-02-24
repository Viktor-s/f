<?php

namespace Furniture\FrontendBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Furniture\FrontendBundle\Repository\Query\FactoryQuery;
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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Constructor
     *
     * @param FactoryRetailerRelationRepository $factoryRelationRepository
     * @param FactoryRepository                 $factoryRepository
     * @param TokenStorageInterface             $tokenStorage
     */
    public function __construct(
        FactoryRetailerRelationRepository $factoryRelationRepository,
        FactoryRepository $factoryRepository,
        TokenStorageInterface $tokenStorage
    )
    {
        $this->factoryRelationRepository = $factoryRelationRepository;
        $this->factoryRepository = $factoryRepository;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class'   => FactoryRetailerRelation::class,
            'factory_user' => null,
            'content_user' => null,
            'factory'      => null,
        ]);

        $resolver->setRequired('mode');
        $resolver->setAllowedValues('mode', ['from_factory', 'from_retailer']);
        $resolver->setAllowedTypes('factory_user', ['null', User::class]);
        $resolver->setAllowedTypes('content_user', ['null', User::class]);
        $resolver->setAllowedTypes('factory', ['null', Factory::class]);

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
                        'label'    => 'frontend.retailer',
                        'mapped'   => false,
                        'disabled' => true,
                        'data'     => $relation->getRetailer()->getName(),
                    ]);
                } else {
                    $form->add('retailer', 'entity', [
                        'label' => 'frontend.retailer',
                        'class' => RetailerProfile::class,
                        'query_builder' => function (EntityRepository $er) use ($relation) {
                            if ($relation->getFactory()) {
                                return $er->createQueryBuilder('r')
                                    ->leftJoin(FactoryRetailerRelation::class, 'frr', 'WITH', 'frr.retailer = r AND frr.factory = :factory')
                                    ->andWhere('frr.factory IS NULL')
                                    // Use group by instead distinct() because of ERROR:  could not identify an equality operator for type json
                                    ->groupBy('r.id')
                                    ->setParameter('factory', $relation->getFactory()->getId());
                            } else {
                                return $er->createQueryBuilder('r');
                            }
                        }
                    ]);
                }
            }

            if ($options['mode'] == 'from_retailer') {
                if ($relation->getId()) {
                    // Edit saved relation
                    $form->add('_factory', 'text', [
                        'label'    => 'frontend.brand',
                        'mapped'   => false,
                        'disabled' => true,
                        'data'     => $relation->getFactory()->getName(),
                    ]);
                } else {
                    if ($options['factory']) {
                        /** @var \Furniture\FactoryBundle\Entity\Factory $factory */
                        $factory = $options['factory'];

                        $form->add('_factory', 'text', [
                            'label'    => 'frontend.brand',
                            'mapped'   => false,
                            'disabled' => true,
                            'data'     => $factory->getName(),
                        ]);
                    } else {
                        $form->add('factory', 'entity', [
                            'label'         => 'frontend.factory',
                            'class'         => Factory::class,
                            'query_builder' => function (EntityRepository $er) use ($relation) {
                                if ($relation->getRetailer()) {
                                    $factoryQuery = new FactoryQuery();
                                    $activeUser = $this->tokenStorage->getToken()->getUser();
                                    $factoryQuery->withRetailerFromUser($activeUser);
                                    $factoryQuery->withoutRetailerAccessControl();

                                    if ($relation->getRetailer()->isDemo()) {
                                        $factoryQuery->withoutOnlyEnabledOrDisabled();
                                    }

                                    $qb = $this->factoryRepository->createQueryBuilderForFactory($factoryQuery);

                                    return $qb
                                        ->leftJoin('f.retailerRelations', 'fur', 'WITH', 'fur.retailer = :retailer')
                                        ->andWhere('fur.retailer is NULL')
                                        ->setParameter('retailer', $relation->getRetailer());
                                } else {
                                    return $er->createQueryBuilder('f');
                                }
                            },
                        ]);
                    }
                }
            }

            $form
                ->add('accessProducts', 'checkbox', [
                    'label'    => 'frontend.products_view',
                    'disabled' => $disabledAccessRights,
                    'required' => false,
                ])
                ->add('accessProductsPrices', 'checkbox', [
                    'label'    => 'frontend.product_prices_view',
                    'disabled' => $disabledAccessRights,
                    'required' => false,
                ])
                ->add('discount', 'number', [
                    'label'    => 'frontend.discount',
                    'disabled' => $disabledAccessRights,
                ]);
                if ($relation->getId() && $relation->isDeal() ) {
                    $form->add('active', 'checkbox', [
                        'label'    => 'is Active',
                    ]);
                }
                $form->add('_submit', 'submit', [
                    'label' => 'frontend.save',
                    'attr'  => [
                        'class' => 'btn btn-success col-lg-offset-2',
                    ],
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
