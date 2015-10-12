<?php

namespace Furniture\FrontendBundle\Controller;

use Furniture\FrontendBundle\Repository\FactoryRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FactoryController
{
    
    /**
     * @var \Twig_Environment
     */
    private $twig;
    
    /**
     * @var FactoryRepository
     */
    private $factoryRepository;
    
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    
    public function __construct(
        \Twig_Environment $twig,
        FactoryRepository $factoryRepository,
        TokenStorageInterface $tokenStorage
            ){
        
        $this->twig = $twig;
        $this->factoryRepository = $factoryRepository;
        
    }
    
    public function factories(Request $request)
    {
        
        $factories = $this->factoryRepository->findAll();
        
        $content = $this->twig->render('FrontendBundle:FactorySide:factories.html.twig', [
            'factries' => $factories
        ]);

        return new Response($content);
    }
    
    public function general(Request $request, $id)
    {
        
        $content = $this->twig->render('FrontendBundle:FactorySide:general.html.twig', [
            
        ]);

        return new Response($content);
        
    }
    
}

