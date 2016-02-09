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
use Furniture\ProductBundle\Model\GroupVaraintEdit;

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

        $criteria['product'] = $product;

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

        if ($product->isSchematicProductType()) {
            $form = $this->createForm(new ProductPatternWithoutSchemaType(), $pattern, [
                'product' => $product,
            ]);
        } else {
            $form = $this->createForm(new ProductPatternType(), $pattern, [
                'product'     => $product,
                'parts'       => $product->getProductParts(),
                'sku_options' => $product->getSkuOptionVariants(),
            ]);
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($product->isSchematicProductType()) {
                // This is a schematic product type, and user only choice scheme. Redirect to full form
                $url = $this->generateUrl('furniture_backend_product_pattern_create_with_scheme', [
                    'productId' => $product->getId(),
                    'scheme'    => $pattern->getScheme()->getId(),
                ]);
            } else {
                // This is a simple product type, and we should save pattern and redirect to index.
                $em = $this->get('doctrine.orm.default_entity_manager');
                $em->persist($pattern);
                $em->flush();

                $url = $this->generateUrl('furniture_backend_product_pattern_index', [
                    'productId' => $product->getId(),
                ]);
            }

            return new RedirectResponse($url);
        }

        $groupVariantFilter = null;

        if ($product->isNotSchematicProductType()) {
            $groupVariantFilter = new GroupVaraintEdit($product);
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('create.html'))
            ->setData([
                'product'                        => $product,
                $this->config->getResourceName() => $pattern,
                'form'                           => $form->createView(),
                'groupVariantFilter'             => $groupVariantFilter,
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
            'product'     => $product,
            'parts'       => $scheme->getProductParts(),
            'sku_options' => $product->getSkuOptionVariants(),
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');
            $em->persist($pattern);
            $em->flush();

            $toUrl = $this->get('router')->generate('furniture_backend_product_pattern_index', [
                'productId' => $product->getId(),
            ]);

            return new RedirectResponse($toUrl);
        }

        if ($product->isSchematicProductType()) {
            $groupVariantFilter = new GroupVaraintEdit($product, $scheme);
        } else {
            $groupVariantFilter = new GroupVaraintEdit($product);
        }
        
        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('create.html'))
            ->setData([
                'product'                        => $product,
                $this->config->getResourceName() => $pattern,
                'form'                           => $form->createView(),
                'scheme'                         => $scheme,
                'groupVariantFilter'             => $groupVariantFilter,
            ]);

        return $this->handleView($view);
    }

    /**
     * Update action
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request)
    {
        $this->isGrantedOr403('update');
        $product = $this->loadProduct($request);

        /** @var ProductVariantsPattern $pattern */
        $pattern = $this->findOr404($request);

        if ($pattern->getProduct()->getId() !== $product->getId()) {
            throw new NotFoundHttpException('The project identifiers not equals.');
        }

        if ($product->isSchematicProductType()) {
            $parts = $pattern->getScheme()->getProductParts();
        } else {
            $parts = $product->getProductParts();
        }

        $form = $this->createForm(new ProductPatternType(), $pattern, [
            'product'     => $product,
            'parts'       => $parts,
            'sku_options' => $product->getSkuOptionVariants(),
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            // Save
            $this->get('doctrine.orm.default_entity_manager')->flush();

            $url = $this->get('router')->generate('furniture_backend_product_pattern_index', [
                'productId' => $product->getId(),
            ]);

            return new RedirectResponse($url);
        }

        if ($product->isSchematicProductType()) {
            $groupVariantFilter = new GroupVaraintEdit($product, $pattern->getScheme());
        } else {
            $groupVariantFilter = new GroupVaraintEdit($product);
        }
        
        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('update.html'))
            ->setData([
                $this->config->getResourceName() => $pattern,
                'form'                           => $form->createView(),
                'product'                        => $product,
                'pattern'                        => $pattern,
                'groupVariantFilter'             => $groupVariantFilter,
            ]);

        return $this->handleView($view);
    }

    /**
     * Delete action
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request)
    {
        $this->isGrantedOr403('delete');

        $product = $this->loadProduct($request);
        /** @var ProductVariantsPattern $pattern */
        $pattern = $this->findOr404($request);

        if ($product->getId() != $pattern->getProduct()->getId()) {
            throw new NotFoundHttpException('Product requested not equals.');
        }

        // @todo: check constraints before remove from database

        $em = $this->get('doctrine.orm.default_entity_manager');

        $em->remove($pattern);
        $em->flush();

        $url = $this->get('router')->generate('furniture_backend_product_pattern_index', [
            'productId' => $product->getId(),
        ]);

        return new RedirectResponse($url);
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
