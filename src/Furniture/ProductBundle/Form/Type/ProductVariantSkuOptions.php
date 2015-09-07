<?php

namespace Furniture\ProductBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ProductVariantSkuOptions extends AbstractType {
    
    /**
     *
     * @var \Furniture\ProductBundle\Entity\ProductVariant
     */
    protected $variant;
    
    /**
     * 
     * @param \Furniture\ProductBundle\Entity\ProductVariant $variant
     */
    function __construct($variant) {
        $this->variant = $variant;
    }
    
    /**
     * 
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options){
        
        $data = [];
        foreach($this->variant->getSkuOptions() as $sku_option){
            $data[$sku_option->getSkuOptionType()->getId()] = $sku_option;
        }
        
        $i = 0;
        foreach($this->variant->getProduct()->getSkuOptionVariantsGrouped() as $grouped){
            $sku_option_type = $grouped[0]->getSkuOptionType();
            $builder->add( $i, 'entity', [
                'class' => 'Furniture\SkuOptionBundle\Entity\SkuOptionVariant',
                'choice_label' => 'value',
                'label' => $sku_option_type->getName(),
                'choices' => $grouped,
                'data' => isset($data[$sku_option_type->getId()]) ? $data[$sku_option_type->getId()] : null,
            ]);
            $i ++;
        }
        
    }
    
    public function getName() {
        return 'product_variant_sku_options';
    }

}

