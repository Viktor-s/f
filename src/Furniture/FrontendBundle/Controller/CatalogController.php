<?php

namespace Furniture\FrontendBundle\Controller;

use Furniture\FrontendBundle\Repository\ProductRepository;
use Furniture\FrontendBundle\Repository\SpecificationItemRepository;
use Furniture\FrontendBundle\Repository\SpecificationRepository;
use Furniture\ProductBundle\Entity\ProductVariant;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;

class CatalogController {

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
     * @var \Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonRepository
     */
    private $taxonRepository;

    /**
     *
     * @var \Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonomyRepository
     */
    private $taxonomyRepository;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    private $paginator;

    /**
     * Construct
     *
     * @param \Twig_Environment           $twig
     * @param ProductRepository           $productRepository
     * @param SpecificationRepository     $specificationRepository
     * @param SpecificationItemRepository $specificationItemRepository
     * @param TokenStorageInterface       $tokenStorage
     */
    public function __construct(
    \Twig_Environment $twig, \Doctrine\ORM\EntityRepository $productRepository, \Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonRepository $taxonRepository, EntityRepository $taxonomyRepository, SpecificationRepository $specificationRepository, SpecificationItemRepository $specificationItemRepository, TokenStorageInterface $tokenStorage
    ) {
        $this->twig = $twig;
        $this->productRepository = $productRepository;
        $this->taxonRepository = $taxonRepository;
        $this->taxonomyRepository = $taxonomyRepository;
        $this->specificationRepository = $specificationRepository;
        $this->specificationItemRepository = $specificationItemRepository;
        $this->tokenStorage = $tokenStorage;
    }

    protected function getAllChildTaxons($taxon) {
        $taxons = [];
        foreach ($taxon->getChildren() as $child) {
            $taxons[] = $child;
            $taxons = array_merge($taxons, $this->getAllChildTaxons($child));
        }
        return $taxons;
    }
    
    protected function getCategoryTaxonomy(){
        return $this->taxonomyRepository->findOneBy(['name' => 'Category']);
    }


    public function taxonomy(Request $request) {
        $user = $this->tokenStorage->getToken()
                ->getUser();

        $category = $this->getCategoryTaxonomy();
        
        $content = $this->twig->render('FrontendBundle:Catalog:taxonomy.html.twig', [
            'Category' => $category,
        ]);

        return new Response($content);
    }

    public function products(Request $request, $category_permalink) {
        $user = $this->tokenStorage->getToken()
                ->getUser();
        
        /* Getting root taxon by permalink */
        $root_taxon = $this->taxonRepository
                ->findOneBy(['permalink' => $category_permalink]);

        $child_taxons = [];
        /* if selected sub category, get products from sub category */
        if( $subcategory = $request->get('subcategory', null) ){
            if( $subcategory = $this->taxonRepository->findOneById($subcategory) ){
                $child_taxons = $this->getAllChildTaxons($subcategory);
                $child_taxons[] = $subcategory;
            }
        }else{
            $child_taxons = $this->getAllChildTaxons($root_taxon);
        }

        /* Build product query */
        $page = $request->get('page', 1);
        $qBuilder = $this->productRepository->createQueryBuilder('p');
        $qBuilder->innerJoin('p.taxons', 'taxon')
                ->andWhere('taxon in ( :taxons )')
                ->setParameter('taxons', $child_taxons)
        ;
        /* Create product paginator */
        $products = $this->productRepository->getPaginator($qBuilder);
        $products->setMaxPerPage(12);
        $products->setCurrentPage($page);
        
        $content = $this->twig->render('FrontendBundle:Catalog:products.html.twig', [
            'products' => $products, //Paginator object
            'category' => $this->getCategoryTaxonomy(), //Category taxonomy onject
            'current_root_taxon' => $root_taxon, //Current root taxon
            'sub_category' => $subcategory, //if selected child taxon = taxon else null
        ]);

        return new Response($content);
    }

}
