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
        \Twig_Environment $twig,
        \Doctrine\ORM\EntityRepository $productRepository,
        \Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonRepository $taxonRepository,
        EntityRepository $taxonomyRepository,
        SpecificationRepository $specificationRepository,
        SpecificationItemRepository $specificationItemRepository,
        TokenStorageInterface $tokenStorage
    ) {
        $this->twig = $twig;
        $this->productRepository = $productRepository;
        $this->taxonRepository = $taxonRepository;
        $this->taxonomyRepository = $taxonomyRepository;
        $this->specificationRepository = $specificationRepository;
        $this->specificationItemRepository = $specificationItemRepository;
        $this->tokenStorage = $tokenStorage;
    }
    
    public function taxonomy(Request $request){
        $user = $this->tokenStorage->getToken()
            ->getUser();
        
        $category = $this->taxonomyRepository->findOneBy(['name' => 'Category']);
        
        $content = $this->twig->render('FrontendBundle:Catalog:taxonomy.html.twig', [
            'Category' => $category,
        ]);

        return new Response($content);
    }
    
    protected function getAllChildTaxons($taxon)
    {
        $taxons = [];
        foreach($taxon->getChildren() as $child){
            $taxons[] = $child;
            $taxons = array_merge($taxons, $this->getAllChildTaxons($child));
        }
        return $taxons;
    }
    
    public function products(Request $request, $category_permalink)
    {
        $user = $this->tokenStorage->getToken()
            ->getUser();
        
        $taxon = $this->taxonRepository
                ->findOneBy(['permalink' => $category_permalink]);
        
        $child_taxons = $this->getAllChildTaxons($taxon);
        
        $page = $request->get('page', 1);
        
        $qBuilder = $this->productRepository->createQueryBuilder('p');
        
        //if($taxons = $request->get('taxons', false)){
          //  $taxons = array_map( function($v){ return (int)$v; }, $taxons);
            
            $qBuilder->innerJoin('p.taxons', 'taxon')
                ->andWhere('taxon in ( :taxons )')
                ->setParameter('taxons', $child_taxons)
                ;
            
       // }
        
        $products = $this->productRepository->getPaginator($qBuilder);
        
        $products->setMaxPerPage(12);
        $products->setCurrentPage($page);
        $content = $this->twig->render('FrontendBundle:Catalog:products.html.twig', [
            'products' => $products,
        ]);

        return new Response($content);
        
    }
    
}

