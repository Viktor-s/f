<?php

namespace Furniture\ProductBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Furniture\FactoryBundle\Entity\Factory;
use Symfony\Component\Form\AbstractType;
use Furniture\ProductBundle\Entity\ProductPartType;
use Furniture\ProductBundle\Entity\ProductPart;
use Furniture\ProductBundle\Entity\ProductPartMaterial;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\Common\Collections\ArrayCollection;

class ProductPartFormType extends AbstractType
{
    /**
     * @var Factory
     */
    private $factory;

    /**
     * Construct
     *
     * @param Factory $factory
     */
    public function __construct(Factory $factory = null)
    {
        $this->factory = $factory;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProductPart::class
        ]);
    }
    
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('translations', 'a2lix_translationsForms', [
                'form_type' => new ProductPartTranslationFormType(),
                'empty_data' => function($form){
                    return new ArrayCollection;
                },
            ])
            ->add('productPartMaterials', 'entity', [
                'class' => ProductPartMaterial::class,
                'query_builder' => function (EntityRepository $er) {
                    if ($this->factory) {
                        return $er->createQueryBuilder('ppm')
                            ->leftJoin('ppm.factory', 'f')
                            ->andWhere('f.id IS NULL OR f.id = :factory')
                            ->setParameter('factory', $this->factory->getId());
                    } else {
                        return $er->createQueryBuilder('ppm');
                    }
                },
                'multiple' => true,
                'expanded' => false
            ])
            ->add('productPartType', 'entity', [
                'class' => ProductPartType::class,
                'multiple' => false
            ]);

    }
    
    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'product_part';
    }
    
}

