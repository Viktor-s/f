<?php

namespace Furniture\ProductBundle\Controller;

use Sylius\Bundle\ProductBundle\Controller\VariantController as BaseVariantController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
        $this->getGenerator()->generate($product);

        $manager = $this->get('sylius.manager.product');
        $manager->persist($product);
        $manager->flush();

        $this->flashHelper->setFlash('success', 'generate');

        return $this->redirectHandler->redirectTo($product);
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
