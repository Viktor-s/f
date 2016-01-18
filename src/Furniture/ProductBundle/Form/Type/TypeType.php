<?php

namespace Furniture\ProductBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Furniture\ProductBundle\Entity\Type;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TypeType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Type::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Type $data */
        $data = $builder->getData();

        $builder
            ->add('parent', 'entity', [
                'label' => 'Parent',
                'class' => Type::class,
                'required' => false,
                'query_builder' => function (EntityRepository $er) use ($data) {
                    $qb = $er->createQueryBuilder('t');

                    if ($data && $data->getId()) {
                        $qb
                            ->andWhere('t.id != :id')
                            ->setParameter('id', $data->getId());
                    }

                    return $qb;
                }
            ])
            ->add('slug', 'text', [
                'label' => 'Slug'
            ])
            ->add('position', 'number', [
                'label' => 'Postition',
                'attr' => array('value' => 0)    
            ])
            ->add('translations', 'a2lix_translationsForms', [
                'form_type' => new TypeTranslationType()
            ]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var Type $type */
            $type = $event->getData();

            if (null === $type->getPosition()) {
                $type->setPosition(0);
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_type';
    }
}
