<?php

namespace Furniture\ProductBundle\Generator;

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\DependencyInjection\ContainerAware;
use Sylius\Component\Variation\SetBuilder\SetBuilderInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Furniture\ProductBundle\Entity\Product;
use Sylius\Component\Variation\Model\VariantInterface;
use Symfony\Component\Validator\ValidatorInterface;


class VariantGenerator extends ContainerAware {
    
    protected $setBuilder;
    
    protected $eventDispatcher;
    
    protected $variantRepository;
    
    function __construct(RepositoryInterface $variantRepository, SetBuilderInterface $setBuilder, ValidatorInterface $validator, EventDispatcherInterface $eventDispatcher) {
        $this->variantRepository = $variantRepository;
        $this->setBuilder = $setBuilder;
        $this->eventDispatcher = $eventDispatcher;
        $this->validator = $validator;
    }
    
    /**
     * 
     * @param \Furniture\ProductBundle\Entity\Product $product
     */
    public function generate( Product $product ) {
        
        if( !$product->hasOptions() && !$product->hasSkuOptionVariants() ){
            throw new \InvalidArgumentException('Cannot generate variants for an product without options and sku options');
        }
        
        $optionSet = array();
        $optionMap = array();
        
        /*
         * Get product option variants
         */
        $pOptionPref = 'o';
        foreach ($product->getOptions() as $k => $option) {
            foreach ($option->getValues() as $value) {
                $optionSet[$pOptionPref.'_'.$k][] = $value->getId();
                $optionMap[$pOptionPref.'_'.$value->getId()] = $value;
            }
        }
        
        /*
         * Get product sku option
         */
        $sOptionPref = 'so';
        foreach($product->getSkuOptionVariantsGrouped() as $k => $option){
            foreach ($option as $value) {
                $optionSet[$sOptionPref.'_'.$k][] = $value->getId();
                $optionMap[$sOptionPref.'_'.$value->getId()] = $value;
            }
        }
        
        //echo '<pre>'.print_r($optionSet,true).'</pre>';
        
        $permutations = $this->setBuilder->build($optionSet);
        foreach ($permutations as $permutation) {
            $variant = $this->variantRepository->createNew();
            $variant->setObject($product);
            $variant->setDefaults($product->getMasterVariant());
            //echo '<pre>'.print_r($permutation,true).'</pre>';
            foreach ($permutation as $map_route => $id) {
                /*
                 * add to product option
                 */
                if(split('_', $map_route)[0] == $pOptionPref ){
                    $variant->addOption($optionMap[$pOptionPref.'_'.$id]);
                }
                                /*
                 * add to sku product option
                 */
                if(split('_', $map_route)[0] == $sOptionPref ){
                    $variant->addSkuOption($optionMap[$sOptionPref.'_'.$id]);
                }
            }
            $product->addVariant($variant);
        }
        //die();
        $this->process($product, $variant);
    }
    
    public function process(Product $variable, VariantInterface $variant)
    {
        $this->eventDispatcher->dispatch('sylius.variant.pre_create', new GenericEvent($variant));
    }

}

