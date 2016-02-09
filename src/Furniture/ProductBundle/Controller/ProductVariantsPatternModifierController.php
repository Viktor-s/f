<?php

namespace Furniture\ProductBundle\Controller;

use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Entity\ProductScheme;
use Furniture\ProductBundle\Entity\ProductVariantsPattern;
use Furniture\ProductBundle\Entity\ProductVariantsPatternModifier;
use Furniture\ProductBundle\Form\Type\Modifier\ModifierPatternType;
use Furniture\ProductBundle\Form\Type\Modifier\ModifierWithoutPatternAndProductType;
use Sylius\Bundle\ResourceBundle\Controller\ResourceController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProductVariantsPatternModifierController extends ResourceController
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
        $sorting['position'] = 'ASC';

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

        $modifier = new ProductVariantsPatternModifier();
        $modifier->setProduct($product);

        if ($product->isSchematicProductType()) {
            $form = $this->createForm(new ModifierWithoutPatternAndProductType(), $modifier, [
                'product' => $product,
            ]);
        } else {
//            $form = $this->createForm(new ProductPatternType(), $modifier, [
//                'product'     => $product,
//                'parts'       => $product->getProductParts(),
//                'sku_options' => $product->getSkuOptionVariants(),
//            ]);
        }

        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($modifier->isAttachedToProduct()) {
                $params = [
                    'productId' => $product->getId(),
                    'attach'    => 'product',
                ];

                if ($modifier->getScheme()) {
                    $params['id'] = $modifier->getScheme()->getId();
                }

                $url = $this->generateUrl('furniture_backend_product_pattern_modifier_create_with_attach', $params);
            } else if ($modifier->isAttachedToPattern()) {
                $url = $this->generateUrl('furniture_backend_product_pattern_modifier_create_with_attach', [
                    'productId' => $product->getId(),
                    'attach'    => 'pattern',
                    'id'        => $modifier->getPattern()->getId(),
                ]);
            } else {
                throw new \RuntimeException('Invalid attach.');
            }

            return new RedirectResponse($url);
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('create.html'))
            ->setData([
                'product'                        => $product,
                $this->config->getResourceName() => $modifier,
                'form'                           => $form->createView(),
            ]);

        return $this->handleView($view);
    }

    /**
     * Create a new modifier with attach
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function createWithAttachAction(Request $request)
    {
        $product = $this->loadProduct($request);

        $modifier = new ProductVariantsPatternModifier();
        $modifier->setProduct($product);

        $attach = $request->attributes->get('attach');
        $attachId = $request->attributes->get('id');

        $parts = null;
        $partVariants = null;
        $skuOptions = null;

        if ($attach == 'pattern') {
            if (!$attachId) {
                throw new NotFoundHttpException('The "id" is requires for attach to pattern.');
            }

            $modifier->setAttach(ProductVariantsPatternModifier::ATTACH_TO_PATTERN);

            $pattern = $this->loadPatternForProduct($product, $attachId);
            $modifier->setPattern($pattern);

            $parts = $pattern->getProductParts();
            $skuOptions = $pattern->getSkuOptionValues();
            $partVariants = $pattern->getProductPartMaterialVariants();
        } else if ($attach == 'product') {
            $modifier->setAttach(ProductVariantsPatternModifier::ATTACH_TO_PRODUCT);
            $scheme = null;

            if ($attachId) {
                $scheme = $this->loadSchemaForProduct($product, $attachId);
                $modifier->setScheme($scheme);

                $parts = $scheme->getProductParts();
            } else {
                $parts = $product->getProductParts();
            }

            $skuOptions = $product->getSkuOptionVariants();
        }

        $form = $this->createForm(new ModifierPatternType(), $modifier, [
            'product'                => $product,
            'sku_options'            => $skuOptions,
            'parts'                  => $parts,
            'part_material_variants' => $partVariants,
            'attach'                 => $modifier->getAttach(),
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.default_entity_manager');

            $em->persist($modifier);
            $em->flush();

            $url = $this->generateUrl('furniture_backend_product_pattern_modifier_index', [
                'productId' => $product->getId(),
            ]);

            return new RedirectResponse($url);
        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('create.html'))
            ->setData([
                'product'                        => $product,
                $this->config->getResourceName() => $modifier,
                'form'                           => $form->createView(),
            ]);

        return $this->handleView($view);
    }

    /**
     * Update modifier
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateAction(Request $request)
    {
        $this->isGrantedOr403('update');
        $product = $this->loadProduct($request);

        /** @var ProductVariantsPatternModifier $modifier */
        $modifier = $this->findOr404($request);

        if ($modifier->getProduct()->getId() !== $product->getId()) {
            throw new NotFoundHttpException('The project identifiers not equals.');
        }

        if ($modifier->isAttachedToPattern()) {
            $parts = $modifier->getPattern()->getProductParts();
            $variants = $modifier->getPattern()->getProductPartMaterialVariants();
            $skuOptions = $modifier->getPattern()->getSkuOptionValues();

        } else if ($modifier->isAttachedToProduct()) {
            if ($modifier->getScheme()) {
                $parts = $modifier->getScheme()->getProductParts();
            } else {
                $parts = $modifier->getProduct()->getProductParts();
            }

            $skuOptions = $modifier->getProduct()->getSkuOptionVariants();
            $variants = null;
        } else {
            throw new \RuntimeException('Invalid modifier attach.');
        }

        $form = $this->createForm(new ModifierPatternType(), $modifier, [
            'parts'                  => $parts,
            'part_material_variants' => $variants,
            'sku_options'            => $skuOptions,
            'product'                => $product,
            'attach'                 => $modifier->getAttach(),
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {

        }

        $view = $this
            ->view()
            ->setTemplate($this->config->getTemplate('update.html'))
            ->setData([
                $this->config->getResourceName() => $modifier,
                'modifier'                       => $modifier,
                'form'                           => $form->createView(),
                'product'                        => $product,
            ]);

        return $this->handleView($view);
    }

    /**
     * Save modifier positions
     *
     * @param Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function savePositionsAction(Request $request)
    {
        $product = $this->loadProduct($request);

        $positions = $request->get('modifier');
        $modifierIds = array_keys($positions);

        $em = $this->get('doctrine.orm.default_entity_manager');

        $modifiers = $em->getRepository(ProductVariantsPatternModifier::class)
            ->findBy([
                'product' => $product,
                'id' => $modifierIds
            ]);

        foreach ($modifiers as $modifier) {
            $modifier->setPosition($positions[$modifier->getId()]);
        }

        $em->flush();

        $url = $this->generateUrl('furniture_backend_product_pattern_modifier_index', [
            'productId' => $product->getId()
        ]);

        return new RedirectResponse($url);
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
        /** @var ProductVariantsPatternModifier $modifier */
        $modifier = $this->findOr404($request);

        if ($product->getId() != $modifier->getProduct()->getId()) {
            throw new NotFoundHttpException('Product requested not equals.');
        }

        // @todo: check constraints before remove from database

        $em = $this->get('doctrine.orm.default_entity_manager');

        $em->remove($modifier);
        $em->flush();

        $url = $this->get('router')->generate('furniture_backend_product_pattern_modifier_index', [
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

    /**
     * Load pattern for product
     *
     * @param Product $product
     * @param int     $pattern
     *
     * @return ProductVariantsPattern
     */
    private function loadPatternForProduct(Product $product, $pattern)
    {
        $pattern = $this->get('doctrine.orm.default_entity_manager')
            ->find(ProductVariantsPattern::class, $patternId = $pattern);

        if (!$pattern) {
            throw new NotFoundHttpException(sprintf(
                'Not found product pattern with id "%s".',
                $patternId
            ));
        }

        if ($pattern->getProduct()->getId() != $product->getId()) {
            throw new NotFoundHttpException(sprintf(
                'The pattern of product "%s [%d]" not equals to requested product "%s [%d]".',
                $pattern->getProduct()->getName(),
                $pattern->getProduct()->getId(),
                $product->getName(),
                $product->getId()
            ));
        }

        return $pattern;
    }
}
