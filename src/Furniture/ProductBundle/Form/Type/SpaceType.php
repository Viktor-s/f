<?php

namespace Furniture\ProductBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Furniture\ProductBundle\Entity\Space;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SpaceType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Space::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Space $data */
        $data = $builder->getData();

        $builder
            ->add('parent', 'entity', [
                'label' => 'Parent',
                'class' => Space::class,
                'required' => false,
                'query_builder' => function (EntityRepository $er) use ($data) {
                    $qb = $er->createQueryBuilder('s');

                    if ($data && $data->getId()) {
                        $qb
                            ->andWhere('s.id != :id')
                            ->setParameter('id', $data->getId());
                    }

                    return $qb;
                }
            ])
            ->add('slug', 'text', [
                'label' => 'Slug'
            ])
            ->add('translations', 'a2lix_translationsForms', [
                'form_type' => new SpaceTranslationType()
            ]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var Space $space */
            $space = $event->getData();

            if (null === $space->getPosition()) {
                $space->setPosition(0);
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_space';
    }
}
