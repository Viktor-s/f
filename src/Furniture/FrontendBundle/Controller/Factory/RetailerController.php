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

class RetailerController {

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

    function __construct(
    \Twig_Environment $twig, 
            EntityManagerInterface $em, 
            TokenStorageInterface $tokenStorage, 
            UrlGeneratorInterface $urlGenerator, 
            FactoryRepository $factoryRepository
    ) {

        $this->twig = $twig;
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->urlGenerator = $urlGenerator;
        $this->factoryRepository = $factoryRepository;
    }

    public function map(Request $request)
    {
        $content = $this->twig->render('FrontendBundle:Factory/Retailer:map.html.twig', [
            ]);
        return new Response($content);
    }
    
}
