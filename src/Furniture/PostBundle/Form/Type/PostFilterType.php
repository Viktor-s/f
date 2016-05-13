<?php

namespace Furniture\PostBundle\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FactoryBundle\Entity\FactoryRetailerRelation;
use Furniture\PostBundle\Entity\Post;
use Furniture\RetailerBundle\Entity\RetailerProfile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class PostFilterType extends AbstractType
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @param $em
     */
    public function __construct($em) {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('factory', 'entity', [
                'class' => Factory::class,
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Factory',
                ],
                'query_builder' => function(EntityRepository $er ) {
                    return $er
                        ->createQueryBuilder('f')
                        ->join(Post::class, 'p', 'WITH', 'p.factory = f.id');
                },
                'data' => !empty($options['data']['factory'])
                    ? $this->em->getReference(Factory::class, $options['data']['factory'])
                    : null,
            ])
            ->add('useOnSlider', 'choice', [
                'choices' => [
                    'all' => 'All',
                    'slider' => 'For slider',
                ],
                'label' => false,
                'attr' => [
                    'placeholder' => 'Usage',
                ]
            ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'posts_filter';
    }
}
