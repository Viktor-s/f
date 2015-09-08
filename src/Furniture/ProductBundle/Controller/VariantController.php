<?php

namespace Furniture\ProductBundle\Controller;

use Sylius\Bundle\ProductBundle\Controller\VariantController as BaseVariantController;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VariantController extends BaseVariantController {
    
    /**
     * Get variant generator.
     *
     * @return \Furniture\ProductBundle\Generator\VariantGenerator
     */
    protected function getGenerator()
    {
        return $this->get('Furniture.generator.product_variant');
    }
    
    /**
     * Generate all possible variants for given product id.
     */
    public function generateAction(Request $request)
    {
        if (null === $productId = $request->get('productId')) {
            throw new NotFoundHttpException('No product given.');
        }

        $product = $this->findProductOr404($productId);
        $this->getGenerator()->generate($product);

        $manager = $this->get('sylius.manager.product');
        $manager->persist($product);
        $manager->flush();

        $this->flashHelper->setFlash('success', 'generate');

        return $this->redirectHandler->redirectTo($product);
    }
    
}

