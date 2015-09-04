<?php

namespace Furniture\ProductBundle\Controller;

use Sylius\Bundle\CoreBundle\Controller\ProductController as BaseProductController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends BaseProductController {
    
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