<?php

namespace Furniture\FrontendBundle\Controller;

use Furniture\FrontendBundle\Repository\FactoryRepository;
use Furniture\FrontendBundle\Repository\ProductRepository;
use Furniture\FrontendBundle\Repository\Query\FactoryQuery;
use Furniture\FrontendBundle\Repository\Query\ProductQuery;
use Furniture\FrontendBundle\Repository\SpecificationItemRepository;
use Furniture\FrontendBundle\Repository\SpecificationRepository;
use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonomyRepository;
use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonRepository;
use Sylius\Component\Core\Model\Taxon;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
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
     * @var SpecificationRepository
     */
    private $specificationRepository;

    /**
     * @var SpecificationItemRepository
     */
    private $specificationItemRepository;

    /**
     *
     * @var TaxonRepository
     */
    private $taxonRepository;

    /**
     * @var TaxonomyRepository
     */
    private $taxonomyRepository;

    /**
     * @var FactoryRepository
     */
    private $factoryRepository;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Construct
     *
     * @param \Twig_Environment           $twig
     * @param ProductRepository           $productRepository
     * @param FactoryRepository           $factoryRepository
     * @param TaxonRepository             $taxonRepository
     * @param TaxonomyRepository          $taxonomyRepository
     * @param SpecificationRepository     $specificationRepository
     * @param SpecificationItemRepository $specificationItemRepository
     * @param TokenStorageInterface       $tokenStorage
     */
    public function __construct(
        \Twig_Environment $twig,
        ProductRepository $productRepository,
        FactoryRepository $factoryRepository,
        TaxonRepository $taxonRepository,
        TaxonomyRepository $taxonomyRepository,
        SpecificationRepository $specificationRepository,
        SpecificationItemRepository $specificationItemRepository,
        TokenStorageInterface $tokenStorage
    ) {
        $this->twig = $twig;
        $this->productRepository = $productRepository;
        $this->factoryRepository = $factoryRepository;
        $this->taxonRepository = $taxonRepository;
        $this->taxonomyRepository = $taxonomyRepository;
        $this->specificationRepository = $specificationRepository;
        $this->specificationItemRepository = $specificationItemRepository;
        $this->tokenStorage = $tokenStorage;
    }


    /**
     * View category
     *
     * @return Response
     */
    public function taxonomy()
    {
        $category = $this->getCategoryTaxonomy();
        
        $content = $this->twig->render('FrontendBundle:Catalog:taxonomy.html.twig', [
            'category' => $category,
        ]);

        return new Response($content);
    }

    /**
     * Search products
     *
     * @param Request $request
     * @param string  $category The category permalink
     *
     * @return Response
     */
    public function products(Request $request, $category)
    {
        /** @var Taxon $rootTaxon */
        $rootTaxon = $this->taxonRepository->findOneBy([
            'permalink' => $category
        ]);

        if (!$rootTaxon) {
            throw new NotFoundHttpException(sprintf(
                'Not found taxon with permalink "%s",',
                $category
            ));
        }

        $productQuery = new ProductQuery();

        $childTaxons = [];
        /* if selected sub category, get products from sub category */
        if( $subcategory = $request->get('subcategory', null) ){
            /** @var Taxon $subcategory */
            if($subcategory = $this->taxonRepository->find($subcategory)){
                $childTaxons = $this->getAllChildTaxons($subcategory);
                $childTaxons[] = $subcategory;
            }
        }else{
            $childTaxons = $this->getAllChildTaxons($rootTaxon);
        }

        $productQuery
            ->withTaxons($childTaxons);

        $factoryIds = [];
        if ($request->query->has('brand')) {
            $factoryIds = $request->query->get('brand');
            $factoryQuery = new FactoryQuery();
            $factoryQuery->withIds($factoryIds);
            $factories = $this->factoryRepository->findBy($factoryQuery);

            $productQuery
                ->withFactories($factories);
        }

        /** @var \Furniture\CommonBundle\Entity\User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user->isContentUser()) {
            $productQuery->withContentUser($user);
        }
        
        /* Create product paginator */
        $products = $this->productRepository->findBy($productQuery, $request->get('page', 1));
        
        $content = $this->twig->render('FrontendBundle:Catalog:products.html.twig', [
            'products' => $products, //Paginator object
            'category' => $this->getCategoryTaxonomy(), //Category taxonomy onject
            'current_root_taxon' => $rootTaxon, //Current root taxon
            'sub_category' => $subcategory, //if selected child taxon = taxon else null
            'brands' => $this->factoryRepository->findAll(),
            'factory_ids' => $factoryIds,
        ]);

        return new Response($content);
    }

    /**
     * Get all child taxons
     *
     * @param Taxon $taxon
     *
     * @return array
     */
    protected function getAllChildTaxons(Taxon $taxon)
    {
        $taxons = [];

        foreach ($taxon->getChildren() as $child) {
            $taxons[] = $child;
            $taxons = array_merge($taxons, $this->getAllChildTaxons($child));
        }

        return $taxons;
    }

    /**
     * Get category taxonomy
     *
     * @return \Sylius\Component\Core\Model\Taxonomy
     */
    private function getCategoryTaxonomy()
    {
        return $this->taxonomyRepository->findOneBy([
            'name' => 'Category'
        ]);
    }
}
