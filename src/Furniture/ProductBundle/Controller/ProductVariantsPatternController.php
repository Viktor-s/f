<?php

namespace Furniture\ProductBundle\Controller;

use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Entity\ProductScheme;
use Furniture\ProductBundle\Entity\ProductVariantsPattern;
use Furniture\ProductBundle\Form\Type\ProductPattern\ProductPatternType;
use Furniture\ProductBundle\Form\Type\ProductPattern\ProductPatternWithoutSchemaType;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductVariantsPatternController extends ResourceController
{
    /**
     * {@inheritDoc}
     */
    public function indexAction(Request $request)
    {
        $this->isGrantedOr403('index');

        $criteria = $this->config->getCriteria();
        $sorting = $this->config->getSorting();

        $repository = $this->getRepository();
        $product = $this->loadProduct($request);


        $resources = $this->resourceResolver->getResource(
            $repository,
            'findBy',
            [$criteria, $sorting, $this->config->getLimit()]
        );

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('index.html'))
            ->setTemplateVar($this->config->getPluralResourceName())
            ->setData($resources)
            ->setTemplateData([
                'product' => $product,
            ]);

        return $this->handleView($view);
    }

    /**
     * {@inheritDoc}
     */
    public function createAction(Request $request)
    {
        $this->isGrantedOr403('create');
        $product = $this->loadProduct($request);

        $pattern = new ProductVariantsPattern();
        $pattern->setProduct($product);

        $form = $this->createForm(new ProductPatternWithoutSchemaType(), $pattern, [
            'product' => $product,
        ]);

        if ($request->getMethod() === Request::METHOD_POST) {
            $form->submit($request);

            if ($form->isValid()) {
                $url = $this->generateUrl('furniture_backend_product_pattern_create_with_scheme', [
                    'productId' => $product->getId(),
                    'scheme'    => $pattern->getScheme()->getId(),
                ]);

                return new RedirectResponse($url);
            }
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('create.html'))
            ->setData([
                'product'                        => $product,
                $this->config->getResourceName() => $pattern,
                'form'                           => $form->createView(),
            ]);

        return $this->handleView($view);
    }

    /**
     * Create with schema
     *
     * @param Request $request
     * @param integer $scheme
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createWithSchemeAction(Request $request, $scheme)
    {
        $this->isGrantedOr403('create');
        $product = $this->loadProduct($request);
        $scheme = $this->loadSchemaForProduct($product, $scheme);

        $pattern = new ProductVariantsPattern();
        $pattern
            ->setProduct($product)
            ->setScheme($scheme);

        $form = $this->createForm(new ProductPatternType(), $pattern, [
            'product' => $product,
            'scheme'  => $scheme,
        ]);

        if ($request->getMethod() === Request::METHOD_POST) {
            $form->submit($request);

            if ($form->isValid()) {
                // Save
            }
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('create.html'))
            ->setData([
                'product'                        => $product,
                $this->config->getResourceName() => $pattern,
                'form'                           => $form->createView(),
                'scheme'                         => $scheme,
            ]);

        return $this->handleView($view);
    }

    /**
     * Load product
     *
     * @param Request $request
     *
     * @return Product
     */
    private function loadProduct(Request $request)
    {
        $productId = $request->get('productId');

        if (!$productId) {
            throw new NotFoundHttpException('Missing "productId" parameter.');
        }

        $product = $this->get('doctrine.orm.default_entity_manager')
            ->find(Product::class, $productId);

        if (!$product) {
            throw new NotFoundHttpException(sprintf(
                'Not found product with identifier "%s".',
                $productId
            ));
        }

        return $product;
    }

    /**
     * Load schema for product
     *
     * @param Product $product
     * @param int     $scheme
     *
     * @return ProductScheme
     */
    private function loadSchemaForProduct(Product $product, $scheme)
    {
        $scheme = $this->get('doctrine.orm.default_entity_manager')
            ->find(ProductScheme::class, $schemeId = $scheme);

        if (!$scheme) {
            throw new NotFoundHttpException(sprintf(
                'Not found product scheme with id "%s".',
                $schemeId
            ));
        }

        if ($scheme->getProduct()->getId() !== $product->getId()) {
            throw new NotFoundHttpException(sprintf(
                'The scheme product "%s [%d]" not equals to requested product "%s [%d]"',
                $scheme->getProduct()->getName(),
                $scheme->getProduct()->getId(),
                $product->getName(),
                $product->getId()
            ));
        }

        return $scheme;
    }
}
