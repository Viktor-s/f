<?php

namespace Furniture\ProductBundle\Controller;

use Furniture\ProductBundle\Entity\ProductExtension;
use Furniture\ProductBundle\Entity\ProductExtensionVariant;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductExtensionVariantController extends ResourceController
{
    /**
     * {@inheritDoc}
     */
    public function createAction(Request $request)
    {
        $this->isGrantedOr403('create');

        $productExtensionRepository = $this->container->get('doctrine.orm.default_entity_manager')
            ->getRepository(ProductExtension::class);

        $productExtensionId = $request->attributes->get('product_extension');
        /** @var ProductExtension $productExtension */
        $productExtension = $productExtensionRepository->find($productExtensionId);

        if (!$productExtension) {
            throw new NotFoundHttpException(sprintf(
                'Not found product extension with id "%s".',
                $productExtensionId
            ));
        }

        /** @var ProductExtensionVariant $resource */
        $resource = $this->createNew();
        $resource->setExtension($productExtension);
        $form = $this->getForm($resource, [
            'product_extension' => $productExtension
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->domainManager->create($form->getData());

            $url = $this->generateUrl('furniture_backend_product_extension_show', [
                'id' => $productExtensionId
            ]);

            return new RedirectResponse($url);
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('create.html'))
            ->setData(array(
                $this->config->getResourceName() => $resource,
                'form'                           => $form->createView(),
                'product_extension'              => $productExtension
            ))
        ;

        return $this->handleView($view);
    }

    /**
     * {@inheritDoc}
     */
    public function updateAction(Request $request)
    {
        $this->isGrantedOr403('update');

        $productExtensionRepository = $this->container->get('doctrine.orm.default_entity_manager')
            ->getRepository(ProductExtension::class);

        $productExtensionId = $request->attributes->get('product_extension');
        /** @var ProductExtension $productExtension */
        $productExtension = $productExtensionRepository->find($productExtensionId);

        if (!$productExtension) {
            throw new NotFoundHttpException(sprintf(
                'Not found product extension with identifier "%s".',
                $productExtensionId
            ));
        }

        /** @var ProductExtensionVariant $resource */
        $resource = $this->findOr404($request);
        $form     = $this->getForm($resource, [
            'product_extension' => $productExtension
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->domainManager->update($resource);

            $url = $this->generateUrl('furniture_backend_product_extension_show', [
                'id' => $productExtensionId
            ]);

            return new RedirectResponse($url);
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('update.html'))
            ->setData(array(
                $this->config->getResourceName() => $resource,
                'form'                           => $form->createView(),
                'product_extension'              => $productExtension
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
        $this->domainManager->delete($this->findOr404($request));

        $productExtensionId = $request->attributes->get('product_extension');

        $url = $this->generateUrl('furniture_backend_product_extension_show', [
            'id' => $productExtensionId
        ]);

        return new RedirectResponse($url);
    }
}
