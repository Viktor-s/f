<?php

namespace Furniture\ProductBundle\Controller;

use Doctrine\ORM\EntityRepository;
use Sylius\Bundle\CoreBundle\Controller\ProductController as BaseProductController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\ExpressionLanguage\ExpressionLanguage;
use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Model\GroupVaraintEdit;

use Furniture\ProductBundle\Form\Type\GroupVariantEditType;

class ProductController extends BaseProductController
{
    /**
     * {@inheritDoc}
     */
    public function deleteAction(Request $request)
    {
        $this->isGrantedOr403('delete');

        $hardDelete = $request->get('hard');
        $em = $this->get('doctrine.orm.default_entity_manager');

        if ($hardDelete) {
            $em->getFilters()->disable('softdeleteable');
        }

        $product = $this->findOr404($request);

        if ($hardDelete) {
            $checker = $this->get('product.removal_checker');
            $removal = $checker->canHardRemove($product);

            if ($removal->notCanRemove()) {
                // @todo: add message to alerts
                return $this->createRedirectResponse($request);
            }
        }

        $this->domainManager->delete($product);

        if ($hardDelete) {
            $em->getFilters()->enable('softdeleteable');
        }

        return $this->createRedirectResponse($request);
    }

    /**
     * Clear deleted products
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @todo: can execute delete query?
     */
    public function clearDeletedAction(Request $request)
    {
        $em = $this->get('doctrine.orm.default_entity_manager');
        $em->getFilters()->disable('softdeleteable');

        $removalChecker = $this->get('product.removal_checker');

        $products = $em->createQueryBuilder()
            ->from(Product::class, 'p')
            ->select('p')
            ->andWhere('p.deletedAt IS NOT NULL AND p.deletedAt <= :now')
            ->setParameter('now', new \DateTime())
            ->getQuery()
            ->getResult();

        foreach ($products as $product) {
            $removal = $removalChecker->canHardRemove($product);

            if ($removal->canRemove()) {
                $em->remove($product);
            }
        }

        $em->flush();

        $em->getFilters()->enable('softdeleteable');

        return $this->createRedirectResponse($request);
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
        $product = $this->findOr404($request);
        
        $form = $this->createForm(new GroupVariantEditType, new \Furniture\ProductBundle\Model\GroupVaraintEdit($product) );
        
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
                    'product' => $product,
                    'form' => $form->createView(),
                    'updatedVariants' => []
                ])
                ->setTemplate($this->config->getTemplate('variantGroupEdit.html'));

        return $this->handleView($view);
    }

    public function autoCompleteActionNoneBundle(Request $request) {
        $response = [];

        if ($term = (string) $request->get('term')) {
            $products = $this->get('sylius.repository.product')->searchNoneBundleByName($term);

            foreach ($products as $product) {
                $response[] = [
                    'label' => $product->getName(),
                    'value' => $product->getId(),
                ];
            }
        }

        return new JsonResponse($response);
    }

    public function autoCompleteAction(Request $request) {

        $response = [];

        if ($term = (string) $request->get('term')) {
            foreach ($this->get('sylius.repository.product')
                    ->searchByName($term) as $product) {
                $response[] = [
                    'label' => $product->getName(),
                    'value' => $product->getId(),
                ];
            }
        }

        return new JsonResponse($response);
    }

    /**
     * {@inheritDoc}
     */
    public function findOr404(Request $request, array $criteria = array())
    {
        // So, the sylius is "govno" and disable softdeletable filter only on repository method,
        // but do not think, what we have a any relations, which are loaded later.
        // And we should get the all relations for load with disable filter

        /** @var \Furniture\ProductBundle\Entity\Product $product */
        $product = parent::findOr404($request, $criteria);

        if ($request->query->get('sdv')) {
            // Use for "Show Deleted Variants"
            $em = $this->get('doctrine.orm.default_entity_manager');

            // Disable filter
            $em->getFilters()->disable('softdeleteable');

            // Load relations
            $product->getVariants();

            // Enable filter
            $em->getFilters()->enable('softdeleteable');
        }

        return $product;
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
