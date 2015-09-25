<?php

namespace Furniture\SpecificationBundle\Form\Type;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CompositionBundle\Form\DataTransformer\CompositeIdModelTransformer;
use Furniture\ProductBundle\Form\DataTransformer\ProductVariantSkuModelTransformer;
use Furniture\SpecificationBundle\Entity\SpecificationItem;
use Furniture\SpecificationBundle\Form\DataTransformer\SpecificationIdModelTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Range;

class SpecificationItemSingleType extends AbstractType
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
            ->add('sku', 'integer', [
                'property_path' => 'productVariant',
                'invalid_message' => 'Invalid SKU'
            ])
            ->add('composite', 'integer', [
                'invalid_message' => 'Invalid composite'
            ])
            ->add('quantity', 'integer')
            ->add('note', 'textarea');

        $builder->get('specification')->addModelTransformer(new SpecificationIdModelTransformer($this->em));
        $builder->get('sku')->addModelTransformer(new ProductVariantSkuModelTransformer($this->em));
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
