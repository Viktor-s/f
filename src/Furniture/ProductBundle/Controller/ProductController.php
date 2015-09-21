<?php

namespace Furniture\ProductBundle\Controller;

use Sylius\Bundle\CoreBundle\Controller\ProductController as BaseProductController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class ProductController extends BaseProductController {
    
    public function variantGroupEditAction(Request $request){
        $this->isGrantedOr403('variantGrouEdit');
        $product = $this->findOr404($request);
        
        $form_builer = $this->createFormBuilder([]);
        
        
        /*Build dynamic form*/
        /*
         * Option values
         */
        $optionsValues = [];
        foreach($product->getOptions() as $k => $option){
            $optionsValues = array_merge($optionsValues, $option->getValues()->toArray());
        }
        if( $optionsValues ){
            $form_builer->add( 'options', 'entity', 
                [
                    'class' => get_class($optionsValues[0]),
                    'label' => 'Option filter',
                    'expanded' => true,
                    'multiple' => true,
                    'query_builder' => function( $er ) use ( $optionsValues ) {
                        return $er->createQueryBuilder('ov')
                                ->where('ov in (:ovs)')->setParameter('ovs', $optionsValues)
                                ;
                    },
                    'data' => $optionsValues,
                    ]  );
        }
        /*
         * Sku option values
         */
        if( $skuOptionsVariants = $product->getSkuOptionVariants() ){
            $form_builer->add( 'sku_options', 'entity', 
                [
                    'class' => get_class($skuOptionsVariants[0]),
                    'label' => 'Sku option filter',
                    'choice_label' => 'value',
                    'expanded' => true,
                    'multiple' => true,
                    'query_builder' => function( $er ) use ( $skuOptionsVariants ) {
                        return $er->createQueryBuilder('sov')
                                ->where('sov in (:sovs)')->setParameter('sovs', $skuOptionsVariants)
                                ;
                    },
                    'data' => clone $skuOptionsVariants,
                    ]  );
        }
        
        /**
         * Common editable values
         */
        $form_builer->add( 'price_calculator', 'text', 
                [
                    'label' => 'Change cost on'
                ]);
        $form_builer->add( 'width', 'text', 
                [
                    'label' => 'Change width on'
                ]);
        $form_builer->add( 'height', 'text', 
                [
                    'label' => 'Change height on'
                ]);
        $form_builer->add( 'depth', 'text', 
                [
                    'label' => 'Change depth on'
                ]);
        $form_builer->add( 'weight', 'text', 
                [
                    'label' => 'Change weight on'
                ]);
        
        /*
         * Delete items flag
         */
        $form_builer->add( 'delete_filtered', 'checkbox', 
                [
                    'label' => 'Delte this items'
                ]);
        
        $form = $form_builer->getForm();
        $filteredVariants = [];
        $form->handleRequest($request);
        if ( $form->isValid() ) {
            $data = $form->getData();
            $options = new ArrayCollection($data['options']);
            $sku_options = new ArrayCollection($data['sku_options']->toArray());
            foreach($product->getVariants() as $variant){
                /*Check sku options*/
                foreach($variant->getSkuOptions() as $skuOption){
                    if(!$sku_options->exists(function($k,$e) use ($skuOption){ return ($e->getId() == $skuOption->getId()); })){
                        continue 2;
                    }
                }
                /*Check options*/
                foreach($variant->getOptions() as $option){
                    if(!$options->contains($option)){
                        continue 2;
                    }
                }
                
                if($data['delete_filtered']){
                    $this->getDoctrine()->getManager()->remove($variant);
                    continue;
                }
                
                $filteredVariants[] = $variant;
                
                $language = new ExpressionLanguage();
                if( $data['price_calculator'] !== null ){
                    $price = $language->evaluate( $data['price_calculator'], ['price' => ($variant->getPrice()/100)] );
                    $variant->setPrice($price*100);
                }
                if( !is_null($data['width']) ){
                    $value = $language->evaluate( $data['width'], ['width' => $variant->getWidth()] );
                    $variant->setWidth($value);
                }
                if( !is_null($data['height']) ){
                    $value = $language->evaluate( $data['height'], ['height' => $variant->getHeight()] );
                    $variant->setHeight($value);
                }
                if( !is_null($data['depth']) ){
                    $value = $language->evaluate( $data['depth'], ['depth' => $variant->getDepth()] );
                    $variant->setDepth($value);
                }
                if( !is_null($data['weight']) ){
                    $value = $language->evaluate( $data['weight'], ['weight' => $variant->getWeight()] );
                    $variant->setWeight($value);
                }
            }
            $this->getDoctrine()->getManager()->flush();
        }
        
        $view = $this
            ->view([
                'product'          => $product,
                'form'             => $form->createView(),
                'updatedVariants'  => $filteredVariants
                    ])
            ->setTemplate($this->config->getTemplate('variantGroupEdit.html'))
        ;

        return $this->handleView($view);
        
    }


    public function autoCompleteActionNoneBundle(Request $request){
        $response = [];
        
        if( $term = (string)$request->get('term') ){
            foreach($this->get('sylius.repository.product')
                    ->searchNoneBundleByName($term) as $product){
                $response[] = [
                    'label' => $product->getName(),
                    'value' => $product->getId(),
                ];
            }
        }
        
        return new JsonResponse($response);
    }
    
    public function autoCompleteAction(Request $request){
        
        $response = [];
        
        if( $term = (string)$request->get('term') ){
            foreach($this->get('sylius.repository.product')
                    ->searchByName($term) as $product){
                $response[] = [
                    'label' => $product->getName(),
                    'value' => $product->getId(),
                ];
            }
        }
        
        return new JsonResponse($response);
    }
    
}