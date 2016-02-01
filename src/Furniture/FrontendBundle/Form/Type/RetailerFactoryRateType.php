<?php

namespace Furniture\FrontendBundle\Form\Type;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FactoryBundle\Entity\RetailerFactoryRate;
use Furniture\FrontendBundle\Repository\FactoryRepository;
use Furniture\FrontendBundle\Repository\Query\FactoryQuery;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class RetailerFactoryRateType extends AbstractType
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var FactoryRepository
     */
    private $factoryRepository;

    /**
     * Construct
     *
     * @param FactoryRepository     $factoryRepository
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(FactoryRepository $factoryRepository, TokenStorageInterface $tokenStorage)
    {
        $this->factoryRepository = $factoryRepository;
        $this->tokenStorage = $tokenStorage;
    }

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

        if (!$factoryRate->getId()) {
            $builder
                ->add('factory', 'entity', [
                    'class'         => Factory::class,
                    'label'         => 'frontend.factory',
                    'query_builder' => function (EntityRepository $er) use ($factoryRate) {
                        if ($factoryRate->getRetailer()) {
                            $factoryQuery = new FactoryQuery();
                            $activeUser = $this->tokenStorage->getToken()->getUser();
                            $factoryQuery->withRetailerFromUser($activeUser);

                            $qb = $this->factoryRepository->createQueryBuilderForFactory($factoryQuery);

                            return $qb
                                ->leftJoin('f.retailerRelations', 'fur', 'WITH', 'fur.retailer = :retailer')
                                ->andWhere('fur.retailer is NULL')
                                ->setParameter('retailer', $factoryRate->getRetailer());
                        } else {
                            return $er->createQueryBuilder('f');
                        }
                    },
                ]);
        } else {
            $builder
                ->add('factory', 'entity', [
                    'class'    => Factory::class,
                    'label'    => 'frontend.factory',
                    'data'     => $factoryRate->getFactory(),
                    'disabled' => true,
                ]);
        }

        $builder
            ->add('coefficient', 'number', [
                'label' => 'frontend.coefficient',
            ])
            ->add('dumping', 'number', [
                'label' => 'frontend.dumping',
            ])
            ->add('_submit', 'submit', [
                'label' => 'frontend.save',
                'attr'  => [
                    'class' => 'btn btn-primary',
                ],
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'retailer_factory_rate';
    }
}