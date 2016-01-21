<?php

namespace Furniture\FrontendBundle\Controller;

use Furniture\FrontendBundle\Repository\ProductRepository;
use Furniture\FrontendBundle\Repository\Query\ProductQuery;
use Furniture\FrontendBundle\Repository\SpecificationItemRepository;
use Furniture\FrontendBundle\Repository\SpecificationRepository;
use Furniture\ProductBundle\Entity\ProductVariant;
use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProductController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var ProductRepository
     */
    private $productRepository;

    /**
     * @var SpecificationRepository
     */
    private $specificationRepository;

    /**
     * @var SpecificationItemRepository
     */
    private $specificationItemRepository;

    /**
     * @var TaxonRepository
     */
    private $taxonRepository;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * Construct
     *
     * @param \Twig_Environment             $twig
     * @param ProductRepository             $productRepository
     * @param TaxonRepository               $taxonRepository
     * @param SpecificationRepository       $specificationRepository
     * @param SpecificationItemRepository   $specificationItemRepository
     * @param TokenStorageInterface         $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        \Twig_Environment $twig,
        ProductRepository $productRepository,
        TaxonRepository $taxonRepository,
        SpecificationRepository $specificationRepository,
        SpecificationItemRepository $specificationItemRepository,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->twig = $twig;
        $this->productRepository = $productRepository;
        $this->specificationRepository = $specificationRepository;
        $this->specificationItemRepository = $specificationItemRepository;
        $this->taxonRepository = $taxonRepository;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function products(Request $request, $page)
    {
        $productQuery = new ProductQuery();

        if ($taxons = $request->get('taxons', [])) {
            $taxons = $this->taxonRepository->findBy([
                'id' => $taxons,
            ]);

            $productQuery->withSpaces($taxons);

        }

        $products = $this->productRepository->findBy($productQuery, $page, 12);

        $content = $this->twig->render('FrontendBundle:Product:products.html.twig', [
            'products' => $products,
        ]);

        return new Response($content);

    }

    /**
     * Show product action
     *
     * @param Request $request
     * @param int     $product
     *
     * @return Response
     */
    public function product(Request $request, $product)
    {
        $user = $this->tokenStorage->getToken()
            ->getUser();

        $product = $this->productRepository->find($productId = $product);

        if (!$product) {
            throw new NotFoundHttpException(sprintf(
                'Not found product with identifier "%s".',
                $productId
            ));
        }

        if (!$product->isAvailable()) {
            throw new NotFoundHttpException(sprintf(
                'The product with id "%s" is not available.',
                $product->getId()
            ));
        }

        $quantity = 1;

        // Get active variant
        $activeVariant = null;
        if ($skuId = $request->query->get('sku_id')) {
            $activeVariant = $product->getVariantById($skuId);

            if (!$activeVariant) {
                throw new NotFoundHttpException(sprintf(
                    'The product with identifier "%s" not have a variant with sku id "%s".',
                    $product->getId(),
                    $skuId
                ));
            }
        }

        // Check, if update for specification item
        $updateSpecificationItem = false;
        $specificationItem = null;
        if ($skuId && $specificationItemId = $request->query->get('si')) {
            $updateSpecificationItem = true;
            $specificationItem = $this->specificationItemRepository->find($specificationItemId);

            if (!$specificationItem) {
                throw new NotFoundHttpException(sprintf(
                    'Not found specification item with identifier "%s".',
                    $specificationItemId
                ));
            }

            if (!$this->authorizationChecker->isGranted('EDIT', $specificationItem)) {
                throw new AccessDeniedException(sprintf(
                    'The user "%s" not have rights for edit specification item with id "%d" on pdp.',
                    $this->tokenStorage->getToken()->getUsername(),
                    $specificationItemId
                ));
            }

            $quantity = $specificationItem->getQuantity();
        }

        // Group options, sku options and min and max price
        $options = [];

        $skuMatrix = [];
        $activeVariantMatrix = false;

        foreach ($product->getVariants() as $variant) {
            /** @var \Furniture\ProductBundle\Entity\ProductVariant $variant */
            $item = [
                'options' => [],
                'variant' => $variant,
            ];

            foreach ($variant->getOptions() as $option) {
                $inputId = $product->getPdpConfig()->findInputForOption($option->getOption())->getId();
                $item['options'][$inputId] = $option->getId();
            }

            foreach ($variant->getSkuOptions() as $option) {
                $inputId = $product->getPdpConfig()->findInputForSkuOption($option->getSkuOptionType())->getId();
                $item['options'][$inputId] = $option->getId();
            }

            foreach ($variant->getProductPartVariantSelections() as $variantSelection) {
                $inputId = $product->getPdpConfig()->findInputForProductPart($variantSelection->getProductPart())->getId();
                $item['options'][$inputId] = $variantSelection->getProductPartMaterialVariant()->getId();
            }

            if ($activeVariant && $variant == $activeVariant) {
                $activeVariantMatrix = $item['options'];
            }

            $skuMatrix[] = $item;
        }

        $specificationsWithBuyer = [];
        $specificationsWithoutBuyer = [];
        foreach( $this->specificationRepository->findOpenedForUser($user) as $specification ){
            if( !$specification->getBuyer() ){
                $specificationsWithoutBuyer[] = $specification;
                continue;
            }
            if( !isset($specificationsWithBuyer[$specification->getBuyer()->getId()]) )
                $specificationsWithBuyer[$specification->getBuyer()->getId()] = [
                    'buyer' => $specification->getBuyer(),
                    'specifications' => []
                ];
            $specificationsWithBuyer[$specification->getBuyer()->getId()]['specifications'][] = $specification;
        }

        $content = $this->twig->render('FrontendBundle:Product:product.html.twig', [
            'product'                   => $product,
            'sku_matrix'                => $skuMatrix,
            'active_variant_matrix'     => $activeVariantMatrix,
            'options'                   => $options,
            'specificationsWithBuyer'            => $specificationsWithBuyer,
            'specificationsWithoutBuyer' => $specificationsWithoutBuyer,
            'specification_item'        => $specificationItem,
            'update_specification_item' => $updateSpecificationItem,
            'quantity'                  => $quantity,
        ]);

        return new Response($content);
    }
}
