<?php

namespace Furniture\SpecificationBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CompositionBundle\Form\DataTransformer\CompositeIdModelTransformer;
use Furniture\ProductBundle\Form\DataTransformer\ProductVariantModelTransformer;
use Furniture\SpecificationBundle\Entity\SpecificationItem;
use Furniture\SpecificationBundle\Entity\SkuSpecificationItem;
use Furniture\SpecificationBundle\Form\DataTransformer\SpecificationIdModelTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class SkuSpecificationItemSingleType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Construct
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SpecificationItem::class
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('specification', 'integer', [
                'invalid_message' => 'Invalid specification.'
            ])
            ->add('id', 'integer', [
                'property_path' => 'skuItem.productVariant',
                'invalid_message' => 'Invalid SKU id'
            ])
            ->add('composite', 'integer', [
                'property_path' => 'skuItem.composite',
                'invalid_message' => 'Invalid composite'
            ])
            ->add('quantity', 'integer')
            ->add('note', 'textarea')
            ->add('position', 'text');

        $builder->get('specification')->addModelTransformer(new SpecificationIdModelTransformer($this->em));
        $builder->get('id')->addModelTransformer(new ProductVariantModelTransformer($this->em));
        $builder->get('composite')->addModelTransformer(new CompositeIdModelTransformer($this->em));
    }

    /**
     * {@inheritDoc}
     */
    public function getName()
    {
        return 'specification_item_single';
    }
}
