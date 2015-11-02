<?php

namespace Furniture\ProductBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Furniture\ProductBundle\Entity\Category;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CategoryType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Category::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Category $data */
        $data = $builder->getData();

        $builder
            ->add('parent', 'entity', [
                'label' => 'Parent',
                'class' => Category::class,
                'required' => false,
                'query_builder' => function (EntityRepository $er) use ($data) {
                    $qb = $er->createQueryBuilder('c');

                    if ($data && $data->getId()) {
                        $qb
                            ->andWhere('c.id != :id')
                            ->setParameter('id', $data->getId());
                    }

                    return $qb;
                }
            ])
            ->add('slug', 'text', [
                'label' => 'Slug'
            ])
            ->add('translations', 'a2lix_translationsForms', [
                'form_type' => new CategoryTranslationType()
            ]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var Category $category */
            $category = $event->getData();

            if (null === $category->getPosition()) {
                $category->setPosition(0);
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_category';
    }
}
