<?php

namespace Furniture\ProductBundle\Controller;

use Sylius\Bundle\ProductBundle\Controller\VariantController as BaseVariantController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Furniture\ProductBundle\Form\Type\GroupVariantFilterType;
use Furniture\ProductBundle\Model\GroupVaraintFiler;
use Furniture\ProductBundle\Form\Type\GroupVariantEditType;
use Furniture\ProductBundle\Model\GroupVaraintEdit;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;

class VariantController extends BaseVariantController
{
    /**
     * {@inheritDoc}
     */
    public function deleteAction(Request $request)
    {
        $hardDelete = $request->get('hard');
        $em = $this->get('doctrine.orm.default_entity_manager');

        if ($hardDelete) {
            $em->getFilters()->disable('softdeleteable');
        }

        /** @var \Furniture\ProductBundle\Entity\ProductVariant $variant */
        $variant = $this->findOr404($request);

        if ($hardDelete) {
            $checker = $this->get('product_variant.removal_checker');
            $removal = $checker->canHardRemove($variant);

            if ($removal->notCanRemove()) {
                // @todo: add message to alerts
                return $this->createRedirectResponse($request);
            }

            $em->getFilters()->enable('softdeleteable');
        }

        $this->domainManager->delete($variant);

        return $this->createRedirectResponse($request);
    }

    /**
     * {@inheritDoc}
     */
    public function restoreAction(Request $request)
    {
        $response = parent::restoreAction($request);

        if ($request->get('_from')) {
            return new RedirectResponse($request->get('_from'));
        }

        return $response;
    }

    /**
     * Clear deleted variants
     *
     * @param Request $request
     * @param int     $productId
     *
     * @return RedirectResponse
     */
    public function clearDeletedAction(Request $request, $productId)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        $em->getFilters()->disable('softdeleteable');

        /** @var \Furniture\ProductBundle\Entity\Product $product */
        $product = $this->findProductOr404($productId);
        $deletedVariants = $product->getDeletedVariants();

        $removalChecker = $this->get('product_variant.removal_checker');

        foreach ($deletedVariants as $variant) {
            $removal = $removalChecker->canHardRemove($variant);

            if ($removal->canRemove()) {
                $em->remove($variant);
            }
        }

        $em->flush();

        $em->getFilters()->enable('softdeleteable');

        if ($request->get('_from')) {
            return new RedirectResponse($request->get('_from'));
        }

        return $this->redirectHandler->redirectToIndex();
    }

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
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function generateAction(Request $request)
    {
        if (null === $productId = $request->get('productId')) {
            throw new NotFoundHttpException('No product given.');
        }

        /** @var \Furniture\ProductBundle\Entity\Product $product */
        $product = $this->findProductOr404($productId);
        $groupVariantFilter = null;
        if( $product->isSchematicProductType() ){
            if( $schemaId = $request->get('schemaId') ){
                foreach($product->getProductSchemes() as $pScheme){
                    if($pScheme->getId() == $schemaId){
                        $groupVariantFilter = new GroupVaraintFiler($product, $pScheme);
                        break;
                    }else{
                        continue;
                    }
                }
                if(!$groupVariantFilter->getScheme())
                    throw new NotFoundHttpException('Incorrect product scheme given.');
            }else{
                $groupVariantFilter = new GroupVaraintFiler($product, $product->getProductSchemes()->first());
            }
        }else{
           $groupVariantFilter = new GroupVaraintFiler($product); 
        }
        
        $form = $this->createForm(new GroupVariantFilterType, $groupVariantFilter);
        
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $filter = $form->getData();
            $generated = $this->getGenerator()->generateByFilter($filter);
            $manager = $this->get('sylius.manager.product');
            $manager->persist($filter->getProduct());
            $manager->flush();
            $this->flashHelper->setFlash(
                        'success', 'Generated '.count($generated)
                );
        }

        $view = $this
                ->view([
                    'groupVariantFilter' => $groupVariantFilter,
                    'form' => $form->createView(),
                ])
                ->setTemplate($this->config->getTemplate('generate.html'));

        return $this->handleView($view);
    }
    
    /**
     * Variant actions
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function variantGroupEditAction(Request $request)
    {
        if (null === $productId = $request->get('productId')) {
            throw new NotFoundHttpException('No product given.');
        }
        
        /** @var \Furniture\ProductBundle\Entity\Product $product */
        $product = $this->findProductOr404($productId);
        $groupVariantFilter = null;
        if( $product->isSchematicProductType() ){
            if( $schemaId = $request->get('schemaId') ){
                foreach($product->getProductSchemes() as $pScheme){
                    if($pScheme->getId() == $schemaId){
                        $groupVariantFilter = new GroupVaraintEdit($product, $pScheme);
                        break;
                    }else{
                        continue;
                    }
                }
                if(!$groupVariantFilter->getScheme())
                    throw new NotFoundHttpException('Incorrect product scheme given.');
            }else{
                $groupVariantFilter = new GroupVaraintEdit($product, $product->getProductSchemes()->first());
            }
        }else{
           $groupVariantFilter = new GroupVaraintEdit($product); 
        }
        
        $form = $this->createForm(new GroupVariantEditType, $groupVariantFilter );
        
        $form->handleRequest($request);

        $filteredVariants = [];
        
        if ($form->isValid()) {
            /**
             * var \Furniture\ProductBundle\Model\GroupVaraintEdit $editObject
             */
            $editObject = $form->getData();

            $filteredVariants = $editObject->getFilteredVariants();
            $removed = 0;
            foreach ($filteredVariants as $variant) {
                if ($form->get('delete_by_filter')->isClicked()) {
                    $this->getDoctrine()->getManager()->remove($variant);
                    $removed ++;
                    continue;
                }
                
                $language = new ExpressionLanguage();

                if ($editObject->getPriceCalculator() !== null) {
                    $price = $language->evaluate($editObject->getPriceCalculator(), [
                        'price' => $variant->getPrice() / 100
                    ]);
                    $variant->setPrice((int) (ceil(($price * 100))));
                }


                if ($editObject->getWidth() !== null) {
                    $value = $language->evaluate($editObject->getWidth(), [
                        'width' => $variant->getWidth()
                    ]);

                    $variant->setWidth($value);
                }

                if ($editObject->getHeight() !== null) {
                    $value = $language->evaluate($editObject->getHeight(), [
                        'height' => $variant->getHeight()
                    ]);

                    $variant->setHeight($value);
                }

                if ($editObject->getDepth() !== null) {
                    $value = $language->evaluate($editObject->getDepth(), [
                        'depth' => $variant->getDepth()
                    ]);
                    $variant->setDepth($value);
                }

                if ($editObject->getWeight() !== null) {
                    $value = $language->evaluate($editObject->getWeight(), [
                        'weight' => $variant->getWeight()
                    ]);
                    $variant->setWeight($value);
                }
                
            }
            $this->getDoctrine()->getManager()->flush();
            if ($form->get('delete_by_filter')->isClicked()) {
                $this->flashHelper->setFlash(
                        'warning', 'Deleted ' . $removed . ' Items'
                );
            } else {
                $this->flashHelper->setFlash(
                        'warning', 'Updated ' . count($filteredVariants) . ' Items'
                );
            }
        }

        $view = $this
                ->view([
                    'updatedVariants' => $filteredVariants,
                    'groupVariantFilter' => $groupVariantFilter,
                    'form' => $form->createView(),
                    'updatedVariants' => []
                ])
                ->setTemplate($this->config->getTemplate('variantGroupEdit.html'));

        return $this->handleView($view);
    }

    /**
     * Create redirect response
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    private function createRedirectResponse(Request $request)
    {
        if ($request->get('_from')) {
            return new RedirectResponse($request->get('_from'));
        }

        return $this->redirectHandler->redirectToIndex();
    }
}
