<?php

namespace Furniture\ProductBundle\Form\Type;

use Furniture\ProductBundle\Entity\ProductVariant;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Furniture\SkuOptionBundle\Entity\SkuOptionVariant;
use Furniture\SkuOptionBundle\Entity\SkuOptionType;

class ProductVariantSkuOptions extends AbstractType
{
    /**
     * @var ProductVariant
     */
    protected $variant;
    
    /**
     * Construct
     *
     * @param ProductVariant $variant
     */
    public function __construct(ProductVariant $variant)
    {
        $this->variant = $variant;
    }
    
    /**
     * {@inheritDoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $data = [];

        foreach($this->variant->getSkuOptions() as $skuOption) {
            $id = $skuOption->getSkuOptionType()->getId();
            $data[$id] = $skuOption;
        }

        foreach($this->variant->getProduct()->getSkuOptionVariantsGrouped() as $grouped){
            /** @var SkuOptionType $skuOptionType */
            $skuOptionType = $grouped[0]->getSkuOptionType();
            $id = $skuOptionType->getId();

            $builder->add( $id, 'entity', [
                'class'        => SkuOptionVariant::class,
                'choice_label' => 'value',
                'label'        => $skuOptionType->getName(),
                'choices'      => $grouped,
                'data'         => isset($data[$id]) ? $data[$id] : null,
                'required'     => false,
            ]);
        }
        
    }
    
    public function getName()
    {
        return 'product_variant_sku_options';
    }
}

