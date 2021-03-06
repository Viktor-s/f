<?php

namespace Furniture\SpecificationBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Furniture\UserBundle\Entity\User;
use Furniture\SpecificationBundle\Entity\Buyer;
use Furniture\SpecificationBundle\Entity\Specification;
use Furniture\SpecificationBundle\Entity\SpecificationSale;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpecificationType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Specification::class
        ]);

        $resolver->setRequired('owner');
        $resolver->setAllowedTypes('owner', [User::class]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('buyer', 'entity', [
                'label' => 'specification.form.buyer',
                'class' => Buyer::class,
                'required' => false,
                'query_builder' => function (EntityRepository $er) use ($options) {
                    return $er->createQueryBuilder('b')
                        ->innerJoin('b.creator', 'rup', 'WITH', 'rup.retailerProfile = :rp')
                        ->setParameter('rp', $options['owner']->getRetailerUserProfile()->getRetailerProfile());
                }
            ])
            ->add('name', 'text', [
                'label' => 'specification.form.name'
            ])
            ->add('description', 'textarea', [
                'label' => 'specification.form.notes_with_will_not_appear_in_specification',
                'required' => false
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'specification';
    }
}
