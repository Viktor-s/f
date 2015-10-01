<?php

namespace Furniture\ProductBundle\Controller;

use Furniture\ProductBundle\Entity\ProductPartMaterial;
use Furniture\ProductBundle\Entity\ProductPartMaterialVariant;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductPartMaterialVariantController extends ResourceController
{
    /**
     * {@inheritDoc}
     */
    public function createAction(Request $request)
    {
        $this->isGrantedOr403('create');

        $productExtensionRepository = $this->container->get('doctrine.orm.default_entity_manager')
            ->getRepository(ProductPartMaterial::class);

        $productExtensionId = $request->attributes->get('product_part_material');
        /** @var ProductPartMaterial $productExtension */
        $productExtension = $productExtensionRepository->find($productExtensionId);

        if (!$productExtension) {
            throw new NotFoundHttpException(sprintf(
                'Not found product part material with id "%s".',
                $productExtensionId
            ));
        }

        /** @var ProductPartMaterialVariant $resource */
        $resource = $this->createNew();
        $resource->setExtension($productExtension);
        $form = $this->getForm($resource, [
            'product_part_material' => $productExtension
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->domainManager->create($form->getData());

            $url = $this->generateUrl('furniture_backend_product_part_material_show', [
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
                'product_part_material'              => $productExtension
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
            ->getRepository(ProductPartMaterial::class);

        $productExtensionId = $request->attributes->get('product_part_material');
        /** @var ProductPartMaterial $productExtension */
        $productExtension = $productExtensionRepository->find($productExtensionId);

        if (!$productExtension) {
            throw new NotFoundHttpException(sprintf(
                'Not found product part material with identifier "%s".',
                $productExtensionId
            ));
        }

        /** @var ProductPartMaterialVariant $resource */
        $resource = $this->findOr404($request);
        $form     = $this->getForm($resource, [
            'product_part_material' => $productExtension
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->domainManager->update($resource);

            $url = $this->generateUrl('furniture_backend_product_part_material_show', [
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
                'product_part_material'              => $productExtension
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

        $productExtensionId = $request->attributes->get('product_part_material');

        $url = $this->generateUrl('furniture_backend_product_part_material_show', [
            'id' => $productExtensionId
        ]);

        return new RedirectResponse($url);
    }
}
