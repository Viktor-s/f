<?php

namespace Furniture\FrontendBundle\Controller\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

use Furniture\FrontendBundle\Repository\FactoryRepository;
use Furniture\FrontendBundle\Repository\ProductStyleRepository;
use Furniture\FrontendBundle\Repository\ProductTypeRepository;
use Furniture\FrontendBundle\Repository\ProductSpaceRepository;
use Furniture\FrontendBundle\Repository\Query\ProductStyleQuery;
use Furniture\FrontendBundle\Repository\Query\ProductTypeQuery;
use Furniture\FrontendBundle\Repository\Query\ProductSpaceQuery;
use Furniture\FrontendBundle\Repository\Query\ProductQuery;

class MainStatisticController 
{
    /**
     *
     * @var \Twig_Environment
     */
    private $twig;
    
    /**
     *
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    private $em;
    
    /**
     *
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    private $tokenStorage;
    
    /**
     *
     * @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface
     */
    private $urlGenerator;
    
    /**
     *
     * @var \Furniture\FrontendBundle\Repository\FactoryRepository
     */
    private $factoryRepository;
    
    /**
     *
     * @var \Furniture\FrontendBundle\Repository\ProductStyleRepository
     */
    private $productStyleRepository;
    
    /**
     *
     * @var \Furniture\FrontendBundle\Repository\ProductStyleRepository
     */
    private $productTypeRepository;
    
    /**
     *
     * @var \Furniture\FrontendBundle\Repository\ProductSpaceRepository
     */
    private $productSpaceRepository;
    
    function __construct(
        \Twig_Environment $twig,
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        UrlGeneratorInterface $urlGenerator,
        FactoryRepository $factoryRepository,
        ProductStyleRepository $productStyleRepository,
        ProductTypeRepository $productTypeRepository,
        ProductSpaceRepository $productSpaceRepository
            ) {
        $this->twig = $twig;
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->urlGenerator = $urlGenerator;
        $this->factoryRepository = $factoryRepository;
        $this->productStyleRepository = $productStyleRepository;
        $this->productTypeRepository = $productTypeRepository;
        $this->productSpaceRepository = $productSpaceRepository;
    }
    
    public function index(Request $request)
    {
        $monthPriod = new \DatePeriod( 
                (new \DateTime())->modify('-1 month'), 
                new \DateInterval('P1D'), 
                new \DateTime());
        $monthShow = [];
        foreach( $monthPriod as $date ){
            $monthShow[] = [ $date->format("Y-m-d"), rand(12, 1000) ];
        }
        
        $monthPriod = new \DatePeriod( 
                (new \DateTime())->modify('-4 month'), 
                new \DateInterval('P1M'), 
                new \DateTime());
        $addedToSpec = [
            'dates' => [],
            'values' => []
        ];
        foreach( $monthPriod as $date ){
            $addedToSpec['dates'][] = $date->format("Y-m-d");
            $addedToSpec['values'][] = rand(12, 85);
        }
        $addedToCloseSpec = [
            'dates' => [],
            'values' => []
        ];
        foreach( $monthPriod as $date ){
            $addedToCloseSpec['dates'][] = $date->format("Y-m-d");
            $addedToCloseSpec['values'][] = rand(12, 85);
        }
        
        $addedClosedSpec = [
         ['added', array_sum($addedToSpec['values'])],
         ['closed', array_sum($addedToCloseSpec['values'])]
        ];
                
        $popularStyle = [];
        foreach($this->productStyleRepository->findAllOnlyRoot() as $root){
            $popularStyle[] = [ rand(0, 10), (string)$root ];
        }
        
        $popularType = [];
        $cnt = 0;
        foreach($this->productTypeRepository->findAllOnlyRoot() as $root){
            $cnt ++;
            $popularType[] = [ rand(0, 10), (string)$root ];
            if($cnt >= 10) break;
        }
        
        $popularSpace = [];
        foreach($this->productSpaceRepository->findAllOnlyRoot() as $root){
            $popularSpace[] = [ rand(0, 10), (string)$root ];
        }
        
        $content = $this->twig->render('FrontendBundle:Factory/MainStatistic:index.html.twig', [
            'monthShow' => $monthShow,
            'addedToSpec' => $addedToSpec,
            'addedToCloseSpec' => $addedToCloseSpec,
            'addedClosedSpec' => $addedClosedSpec,
            'popularStyle' => $popularStyle,
            'popularType' => $popularType,
            'popularSpace' => $popularSpace,
        ]);

        return new Response($content);
    }
    
}

