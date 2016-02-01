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
use Furniture\ProductBundle\Entity\ProductPartVariantSelection;
use Furniture\ProductBundle\Model\GroupVaraintFiler;
use Symfony\Component\Validator\Exception\BadMethodCallException;
use Furniture\ProductBundle\Entity\ProductVariant;

class VariantGenerator extends ContainerAware {

    /**
     *
     * @var \Sylius\Component\Variation\SetBuilder\SetBuilderInterface
     */
    protected $setBuilder;
    
    /**
     *
     * @var \Symfony\Component\EventDispatcher\EventDispatcherInterface
     */
    protected $eventDispatcher;
    
    /**
     *
     * @var \Sylius\Component\Resource\Repository\RepositoryInterface
     */
    protected $variantRepository;
    
    /**
     *
     * @var \Symfony\Component\Validator\ValidatorInterface
     */
    protected $validator;
    
    function __construct(RepositoryInterface $variantRepository, SetBuilderInterface $setBuilder, ValidatorInterface $validator, EventDispatcherInterface $eventDispatcher) {
        $this->variantRepository = $variantRepository;
        $this->setBuilder = $setBuilder;
        $this->eventDispatcher = $eventDispatcher;
        $this->validator = $validator;
    }

    /**
     * 
     * @param GroupVaraintFiler $variant_filter
     * @return \Furniture\ProductBundle\Entity\ProductVariant[]
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function generateByFilter(GroupVaraintFiler $variant_filter)
    {
        if (!$variant_filter->getOptionValues() 
                && !$variant_filter->getSkuOptionsValues() 
                && !$variant_filter->getProductPartMaterialVariants() ) {
            throw new \InvalidArgumentException('Cannot generate variants for without options and sku options');
        }
        
        $optionSet = array();
        $optionMap = array();
        
        /*
         * Get product option variants
         */
        $pOptionPref = 'o';
        foreach($variant_filter->getOptionValues() as $value)
        {
            $route_id = $pOptionPref . '_' . $value->getId();
            $optionSet[$pOptionPref . '_' . $value->getOption()->getId()][$value->getId()] = $route_id;
            $optionMap[$route_id] = $value;
        }
        
        /*
         * Get product sku option
         */
        $sOptionPref = 'so';
        foreach ($variant_filter->getSkuOptionsValues() as $value) {
            $route_id = $sOptionPref . '_' . $value->getId();
            $optionSet[$sOptionPref . '_' . $value->getSkuOptionType()->getId()][$value->getId()] = $route_id;
            $optionMap[$route_id] = $value;
        }
        
        /*
         * Product part
         */
        $pPMaterialPref = 'ppm';
        foreach ($variant_filter->getProductPartMaterialVariants() as $k => $ppv) {
            $pp = $ppv->getProductPart();
            foreach($ppv->getProductPartMaterialVariants() as $mvariant){
                $route_id = $pPMaterialPref . '_' . $mvariant->getId() . '_' . $pp->getId();
                $optionSet[$pPMaterialPref . '_' . $pp->getId()][] = $route_id;
                 $optionMap[$route_id] = [
                        'pp' => $pp,
                        'ppmVariant' => $mvariant
                ];
            }
        }
        
        //echo '<pre>' . print_r($optionSet, true) . '</pre>';

        $permutations = $this->setBuilder->build($optionSet);
        
        //echo '<pre>' . print_r($permutations, true) . '</pre>';
        //die();
        $product = $variant_filter->getProduct();
        
        $generated = [];
        foreach ($permutations as $permutation) {
            $variant = $this->variantRepository->createNew();
            if( $product->isSchematicProductType() ){
                if(!$variant_filter->getScheme())
                    throw new \Exception(__CLASS__.'::'.__METHOD__.'No scheme fount for generate');
                $variant->setProductScheme($variant_filter->getScheme());
            }
            $variant->setObject($product);
            $variant->setDefaults($product->getMasterVariant());
            if($variant_filter->getSkuPrice() !== null){
                $variant->setPrice($variant_filter->getSkuPriceCent());
            }
            if (!is_array($permutation)) {
                $route_id = $permutation;
                /*
                 * add to product option
                 */
                if (split('_', $route_id)[0] == $pOptionPref) {
                    $variant->addOption($optionMap[$route_id]);
                }
                /*
                 * add to sku product option
                 */
                if (split('_', $route_id)[0] == $sOptionPref) {
                    $variant->addSkuOption($optionMap[$route_id]);
                }
                /*
                 * add product parts
                 *
                */
                if (split('_', $route_id)[0] == $pPMaterialPref) {
                    $variant->addProductPartVariantSelection((new ProductPartVariantSelection())
                        ->setProductPart($optionMap[$route_id]['pp'])
                        ->setProductPartMaterialVariant($optionMap[$route_id]['ppmVariant'])
                            );
                }
            } else{
                foreach ($permutation as $k => $route_id) {
                    /*
                     * add to product option
                     */
                    if (split('_', $route_id)[0] == $pOptionPref) {
                        $variant->addOption($optionMap[$route_id]);
                    }
                    /*
                     * add to sku product option
                     */
                    if (split('_', $route_id)[0] == $sOptionPref) {
                        $variant->addSkuOption($optionMap[$route_id]);
                    }
                    /*
                     * add product parts
                     *
                    */
                    if (split('_', $route_id)[0] == $pPMaterialPref) {
                        $variant->addProductPartVariantSelection((new ProductPartVariantSelection())
                        ->setProductPart($optionMap[$route_id]['pp'])
                        ->setProductPartMaterialVariant($optionMap[$route_id]['ppmVariant'])
                            );
                    }
                }
            }

            if($this->validator->validate($variant)->count()){
                throw new \Exception( __METHOD__.' generate invalid '.ProductVariant::class.' instance');
            }
            
            $product->addVariant($variant);
            $generated[] = $variant;
        }

        $this->process($product, $variant);

        return $generated;
    }
    
    /**
     * 
     * @param \Furniture\ProductBundle\Entity\Product $product
     */
    public function generate(Product $product)
    {

        throw new \BadMethodCallException('Deprecated!');
        
        if (!$product->hasOptions() && !$product->hasSkuOptionVariants() && !$product->hasProductParts() ) {
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
                $route_id = $pOptionPref . '_' . $value->getId();
                $optionSet[$pOptionPref . '_' . $k][] = $route_id;
                $optionMap[$route_id] = $value;
            }
        }

        /*
         * Get product sku option
         */
        $sOptionPref = 'so';
        foreach ($product->getSkuOptionVariantsGrouped() as $k => $option) {
            foreach ($option as $value) {
                $route_id = $sOptionPref . '_' . $value->getId();
                $optionSet[$sOptionPref . '_' . $k][] = $route_id;
                $optionMap[$route_id] = $value;
            }
        }
        
        /*
         * Product part
         */
        $pPMaterialPref = 'ppm';
        foreach ($product->getProductParts() as $k => $pp) {
            foreach ($pp->getProductPartMaterials() as $ppMaterial) {
                foreach($ppMaterial->getVariants() as $ppmVariant){
                    $route_id = $pPMaterialPref . '_' . $ppmVariant->getId() . '_' . $pp->getId();
                    $optionSet[$pPMaterialPref . '_' . $k][] = $route_id;
                    $optionMap[$route_id] = [
                        'pp' => $pp,
                        'ppmVariant' => $ppmVariant
                    ];                    
                }
            }
        }

        //echo '<pre>' . print_r($optionSet, true) . '</pre>';

        $permutations = $this->setBuilder->build($optionSet);
        
        //echo '<pre>' . print_r($permutations, true) . '</pre>';
        //die();
        foreach ($permutations as $permutation) {
            $variant = $this->variantRepository->createNew();
            $variant->setObject($product);
            $variant->setDefaults($product->getMasterVariant());
            if (!is_array($permutation)) {
                $route_id = $permutation;
                /*
                 * add to product option
                 */
                if (split('_', $route_id)[0] == $pOptionPref) {
                    $variant->addOption($optionMap[$route_id]);
                }
                /*
                 * add to sku product option
                 */
                if (split('_', $route_id)[0] == $sOptionPref) {
                    $variant->addSkuOption($optionMap[$route_id]);
                }
                /*
                 * add product parts
                 *
                */
                if (split('_', $route_id)[0] == $pPMaterialPref) {
                    $variant->addProductPartVariantSelection((new ProductPartVariantSelection())
                        ->setProductPart($optionMap[$route_id]['pp'])
                        ->setProductPartMaterialVariant($optionMap[$route_id]['ppmVariant'])
                            );
                }
            } else{
                foreach ($permutation as $k => $route_id) {
                    /*
                     * add to product option
                     */
                    if (split('_', $route_id)[0] == $pOptionPref) {
                        $variant->addOption($optionMap[$route_id]);
                    }
                    /*
                     * add to sku product option
                     */
                    if (split('_', $route_id)[0] == $sOptionPref) {
                        $variant->addSkuOption($optionMap[$route_id]);
                    }
                    /*
                     * add product parts
                     *
                    */
                    if (split('_', $route_id)[0] == $pPMaterialPref) {
                        $variant->addProductPartVariantSelection((new ProductPartVariantSelection())
                        ->setProductPart($optionMap[$route_id]['pp'])
                        ->setProductPartMaterialVariant($optionMap[$route_id]['ppmVariant'])
                            );
                    }
                }
            }
            $product->addVariant($variant);
        }
        $this->process($product, $variant);
    }

    public function process(Product $variable, VariantInterface $variant) {
        $this->eventDispatcher->dispatch('sylius.variant.pre_create', new GenericEvent($variant));
    }

}
