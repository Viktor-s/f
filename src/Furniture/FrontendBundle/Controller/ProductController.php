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
     * Construct
     *
     * @param \Twig_Environment           $twig
     * @param ProductRepository           $productRepository
     * @param TaxonRepository             $taxonRepository
     * @param SpecificationRepository     $specificationRepository
     * @param SpecificationItemRepository $specificationItemRepository
     * @param TokenStorageInterface       $tokenStorage
     */
    public function __construct(
        \Twig_Environment $twig,
        ProductRepository $productRepository,
        TaxonRepository $taxonRepository,
        SpecificationRepository $specificationRepository,
        SpecificationItemRepository $specificationItemRepository,
        TokenStorageInterface $tokenStorage
    ) {
        $this->twig = $twig;
        $this->productRepository = $productRepository;
        $this->specificationRepository = $specificationRepository;
        $this->specificationItemRepository = $specificationItemRepository;
        $this->taxonRepository = $taxonRepository;
        $this->tokenStorage = $tokenStorage;
    }

    public function products(Request $request, $page)
    {
        $productQuery = new ProductQuery();
        
        if($taxons = $request->get('taxons', [])) {
            $taxons = $this->taxonRepository->findBy([
                'id' => $taxons
            ]);

            $productQuery->withTaxons($taxons);
            
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

        $quantity = 1;

        // Get active variant
        $activeVariant = null;
        if ($sku_id = $request->query->get('sku_id')) {
            $activeVariant = $product->getVariantById($sku_id);

            if (!$activeVariant) {
                throw new NotFoundHttpException(sprintf(
                    'The product with identifier "%s" not have a variant with sku id "%s".',
                    $product->getId(),
                    $sku_id
                ));
            }
        }

        // Check, if update for specification item
        $updateSpecificationItem = false;
        $specificationItem = null;
        if ($specificationItemSku = $request->query->get('sku_id') && $specificationItemId = $request->query->get('si')) {
            $updateSpecificationItem = true;
            $specificationItem = $this->specificationItemRepository->find($specificationItemId);

            if (!$specificationItem) {
                throw new NotFoundHttpException(sprintf(
                    'Not found specification item with identifier "%s".',
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
               'options_labels' => []
           ];
           
           foreach($variant->getOptions() as $option){
               $item['options']['option_'.$option->getName()] = $option->getValue();
               $item['options_labels']['option_'.$option->getName()] = $option->getName();
           }
           
           foreach($variant->getSkuOptions() as $option){
               $item['options']['skuoption_'.$option->getName()] = $option->getValue();
               $item['options_labels']['skuoption_'.$option->getName()] = $option->getName();
           }
           
           foreach ($variant->getProductPartVariantSelections() as $variant_selection)
           {
               $item['options']['productpart_'.$variant_selection->getProductPart()->getLabel()] 
                       = $variant_selection->getProductPartMaterialVariant()->getName();
               $item['options_labels']['productpart_'.$variant_selection->getProductPart()->getLabel()]
                       = $variant_selection->getProductPart()->getLabel();
           }
           
           if($activeVariant && $variant == $activeVariant){
               $activeVariantMatrix = $item['options'];
           }
           
           $skuMatrix[] = $item;
        }

        // Get min and max prices
        $pricesMap = [];

        $product->getVariants()->forAll(function ($key, ProductVariant $productVariant) use (&$pricesMap) {
            $pricesMap[] = $productVariant->getPrice();

            return true;
        });

        $prices = [
            'min' => min($pricesMap),
            'max' => max($pricesMap)
        ];

        $specifications = $this->specificationRepository->findForUser($user);

        $content = $this->twig->render('FrontendBundle:Product:product.html.twig', [
            'product' => $product,
            'sku_matrix' => $skuMatrix,
            'active_variant_matrix' => $activeVariantMatrix,
            'options' => $options,
            'prices' => $prices,
            'specifications' => $specifications,
            'specification_item' => $specificationItem,
            'update_specification_item' => $updateSpecificationItem,
            'quantity' => $quantity
        ]);

        return new Response($content);
    }
}
