<?php

namespace Furniture\ProductBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Furniture\ProductBundle\Entity\Style;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StyleType extends AbstractType
{
    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Style::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var Style $data */
        $data = $builder->getData();

        $builder
            ->add('parent', 'entity', [
                'label' => 'Parent',
                'class' => Style::class,
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
                'form_type' => new StyleTranslationType()
            ]);

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var Style $style */
            $style = $event->getData();

            if (null === $style->getPosition()) {
                $style->setPosition(0);
            }
        });
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_style';
    }
}
