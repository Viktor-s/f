<?php

namespace Furniture\FrontendBundle\Controller;

use Furniture\FrontendBundle\Repository\CompositeCollectionRepository;
use Furniture\FrontendBundle\Repository\FactoryRepository;
use Furniture\FrontendBundle\Repository\ProductCategoryRepository;
use Furniture\FrontendBundle\Repository\ProductRepository;
use Furniture\FrontendBundle\Repository\ProductSpaceRepository;
use Furniture\FrontendBundle\Repository\ProductStyleRepository;
use Furniture\FrontendBundle\Repository\ProductTypeRepository;
use Furniture\FrontendBundle\Repository\Query\CompositeCollectionQuery;
use Furniture\FrontendBundle\Repository\Query\FactoryQuery;
use Furniture\FrontendBundle\Repository\Query\ProductCategoryQuery;
use Furniture\FrontendBundle\Repository\Query\ProductQuery;
use Furniture\FrontendBundle\Repository\Query\ProductSpaceQuery;
use Furniture\FrontendBundle\Repository\Query\ProductStyleQuery;
use Furniture\FrontendBundle\Repository\Query\ProductTypeQuery;
use Furniture\FrontendBundle\Repository\SpecificationItemRepository;
use Furniture\FrontendBundle\Repository\SpecificationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class CatalogController
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
     * @var ProductCategoryRepository
     */
    private $productCategoryRepository;

    /**
     * @var ProductSpaceRepository
     */
    private $productSpaceRepository;

    /**
     * @var ProductTypeRepository
     */
    private $productTypeRepository;

    /**
     * @var ProductStyleRepository
     */
    private $productStyleRepository;

    /**
     * @var SpecificationRepository
     */
    private $specificationRepository;

    /**
     * @var SpecificationItemRepository
     */
    private $specificationItemRepository;

    /**
     * @var FactoryRepository
     */
    private $factoryRepository;

    /**
     * @var CompositeCollectionRepository
     */
    private $compositeCollectionRepository;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Construct
     *
     * @param \Twig_Environment             $twig
     * @param ProductRepository             $productRepository
     * @param ProductCategoryRepository     $productCategoryRepository
     * @param ProductSpaceRepository        $productSpaceRepository
     * @param ProductStyleRepository        $productStyleRepository
     * @param ProductTypeRepository         $productTypeRepository
     * @param FactoryRepository             $factoryRepository
     * @param SpecificationRepository       $specificationRepository
     * @param SpecificationItemRepository   $specificationItemRepository
     * @param CompositeCollectionRepository $compositeCollectionRepository
     * @param TokenStorageInterface         $tokenStorage
     */
    public function __construct(
        \Twig_Environment $twig,
        ProductRepository $productRepository,
        ProductCategoryRepository $productCategoryRepository,
        ProductSpaceRepository $productSpaceRepository,
        ProductStyleRepository $productStyleRepository,
        ProductTypeRepository $productTypeRepository,
        FactoryRepository $factoryRepository,
        SpecificationRepository $specificationRepository,
        SpecificationItemRepository $specificationItemRepository,
        CompositeCollectionRepository $compositeCollectionRepository,
        TokenStorageInterface $tokenStorage
    ) {
        $this->twig = $twig;
        $this->productRepository = $productRepository;
        $this->productSpaceRepository = $productSpaceRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->productTypeRepository = $productTypeRepository;
        $this->productStyleRepository = $productStyleRepository;
        $this->factoryRepository = $factoryRepository;
        $this->specificationRepository = $specificationRepository;
        $this->specificationItemRepository = $specificationItemRepository;
        $this->compositeCollectionRepository = $compositeCollectionRepository;
        $this->tokenStorage = $tokenStorage;
    }


    /**
     * View category
     *
     * @return Response
     */
    public function catalog()
    {
        $spaces = $this->productSpaceRepository->findAllOnlyRoot();
        
        $content = $this->twig->render('FrontendBundle:Catalog:catalog.html.twig', [
            'spaces' => $spaces,
        ]);

        return new Response($content);
    }

    /**
     * Search products
     *
     * @param Request $request
     *
     * @return Response
     */
    public function products(Request $request)
    {
        $productQuery = new ProductQuery();

        $filterIds = function ($ids) {
            $ids = array_map(function ($i) {
                return (int) $i;
            }, $ids);

            return array_filter($ids);
        };

        $spacesIds = [];
        if ($request->query->has('space')) {
            $spacesIds = (array) $request->query->get('space');
            $spacesIds = $filterIds($spacesIds);

            if ($spacesIds) {
                $spaceQuery = new ProductSpaceQuery();
                $spaceQuery->withIds($spacesIds);
                $spaces = $this->productSpaceRepository->findBy($spaceQuery);

                $productQuery->withSpaces($spaces);
            }
        }

        $categoryIds = [];
        if ($request->query->has('category')) {
            $categoryIds = (array) $request->query->get('category');
            $categoryIds = $filterIds($categoryIds);

            if ($categoryIds) {
                $categoryQuery = new ProductCategoryQuery();
                $categoryQuery->withIds($categoryIds);
                $categories = $this->productCategoryRepository->findBy($categoryQuery);

                $productQuery->withCategories($categories);
            }
        }

        $typeIds = [];
        if ($request->query->has('type')) {
            $typeIds = (array) $request->query->get('type');
            $typeIds = $filterIds($typeIds);

            if ($typeIds) {
                $typeQuery = new ProductTypeQuery();
                $typeQuery->withIds($typeIds);
                $types = $this->productTypeRepository->findBy($typeQuery);

                $productQuery->withTypes($types);
            }
        }

        $styleIds = [];
        if ($request->query->has('style')) {
            $styleIds = (array) $request->query->get('style');
            $styleIds = $filterIds($styleIds);

            if ($styleIds) {
                $styleQuery = new ProductStyleQuery();
                $styleQuery->withIds($styleIds);
                $styles = $this->productStyleRepository->findBy($styleQuery);

                $productQuery->withStyles($styles);
            }
        }

        $compositeCollectionIds = [];
        if ($request->query->has('cc')) {
            $compositeCollectionIds = (array) $request->query->get('cc');
            $compositeCollectionIds = $filterIds($compositeCollectionIds);

            if ($compositeCollectionIds) {
                $compositeCollectionQuery = new CompositeCollectionQuery();
                $compositeCollectionQuery->withIds($compositeCollectionIds);
                $compositeCollections = $this->compositeCollectionRepository->findBy($compositeCollectionQuery);

                $productQuery->withCompositeCollections($compositeCollections);
            }
        }

        $factoryIds = [];
        $compositeСollections = false;
        if ($request->query->has('brand')) {
            $factoryIds = (array) $request->query->get('brand');
            $factoryIds = $filterIds($factoryIds);

            if ($factoryIds) {
                $factoryQuery = new FactoryQuery();
                $factoryQuery->withIds($factoryIds);
                $factories = $this->factoryRepository->findBy($factoryQuery);
                if(count($factories) == 1){
                    $ccQuery = new CompositeCollectionQuery();
                    $ccQuery->withFactories($factories);
                    $compositeСollections = $this->compositeCollectionRepository->findBy($ccQuery);
                }
                $productQuery->withFactories($factories);
            }
        }

        /** @var \Furniture\CommonBundle\Entity\User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user->isRetailer()) {
            $productQuery->withRetailer($user->getRetailerProfile());
        }
        
        /* Create product paginator */
        $products = $this->productRepository->findBy($productQuery, $request->get('page', 1));

        // Create a brands query
        $brandsQuery= new FactoryQuery();
        if ($user->isRetailer()) {
            $brandsQuery->withRetailer($user->getRetailerProfile());
        }
        
        $content = $this->twig->render('FrontendBundle:Catalog:products.html.twig', [
            'products' => $products,
            'brands' => $this->factoryRepository->findBy($brandsQuery),
            'spaces' => $this->productSpaceRepository->findAllOnlyRoot(),
            'categories' => $this->productCategoryRepository->findAllOnlyRoot(),
            'types' => $this->productTypeRepository->findAllOnlyRoot(),
            'styles' => $this->productStyleRepository->findAllOnlyRoot(),
            'composite_collections' => $compositeСollections,

            'factory_ids' => $factoryIds,
            'category_ids' => $categoryIds,
            'space_ids' => $spacesIds,
            'type_ids' => $typeIds,
            'style_ids' => $styleIds,
            'composite_collection_ids' => $compositeCollectionIds
        ]);

        return new Response($content);
    }
}
