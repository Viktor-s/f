<?php

namespace Furniture\ProductBundle\Controller;

use Furniture\ProductBundle\Form\Type\ProductPdpConfigType;
use Sylius\Bundle\CoreBundle\Controller\ProductController as BaseProductController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Furniture\ProductBundle\Entity\Product;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ProductController extends BaseProductController
{
    /**
     * {@inheritDoc}
     */
    public function showAction(Request $request)
    {
        return parent::showAction($request);
    }

    /**
     * {@inheritDoc}
     */
    public function createAction(Request $request)
    {
        $this->isGrantedOr403('create');

        $resource = $this->createNew();
        $form = $this->getForm($resource);

        if ($request->isMethod('POST') && $form->submit($request)->isValid()) {
            $resource = $this->domainManager->create($form->getData());

            $url = $this->generateUrl('sylius_backend_product_update', [
                'id' => $resource->getId()
            ]);

            return $this->redirect($url);
        }

        if ($this->config->isApiRequest()) {
            return $this->handleView($this->view($form, 400));
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('create.html'))
            ->setData(array(
                $this->config->getResourceName() => $resource,
                'form'                           => $form->createView(),
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * {@inheritDoc}
     */
    public function deleteAction(Request $request)
    {
        $this->isGrantedOr403('delete');

        $hardDelete = $request->get('hard');
        $em = $this->get('doctrine.orm.default_entity_manager');
        $checker = $this->get('product.removal_checker');

        if ($hardDelete) {
            $em->getFilters()->disable('softdeleteable');
        }

        $product = $this->findOr404($request);

        if ($hardDelete) {
            $removal = $checker->canHardRemove($product);

            if ($removal->notCanRemove()) {
                $this->flashHelper->setFlash('error', 'Can not hard remove product');

                return $this->createRedirectResponse($request);
            }
        } else {
            $removal = $checker->canRemove($product);

            if ($removal->notCanRemove()) {
                $this->flashHelper->setFlash('error', 'Can not remove product');

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
     * Show/Edit PDP config
     *
     * @param Request $request
     *
     * @return object
     */
    public function editPdpConfig(Request $request)
    {
        $product = $this->findOr404($request);
        $config = $product->getPdpConfig();

        $form = $this->createForm(new ProductPdpConfigType(), $config);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($config);
            $em->flush();

            $url = $this->generateUrl('sylius_backend_product_show', [
                'id' => $product->getId()
            ]);

            return new RedirectResponse($url);
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('update.html'))
            ->setData(array(
                $this->config->getResourceName() => $product,
                'product'                        => $product,
                'form'                           => $form->createView(),
            ));

        return $this->handleView($view);
    }

    /**
     * Autocomplete for bundle
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function autoCompleteActionNoneBundle(Request $request)
    {
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

    /**
     * Action for product autocomplete
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function autoCompleteAction(Request $request)
    {
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
     * {@inheritDoc}
     */
    public function getForm($resource = null, array $options = [])
    {
        /** @var \Furniture\ProductBundle\Entity\Product $resource */
        if (!$resource->getId()) {
            $options['mode'] = 'small';
        } else {
            $options['mode'] = 'full';
        }

        return parent::getForm($resource, $options);
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
