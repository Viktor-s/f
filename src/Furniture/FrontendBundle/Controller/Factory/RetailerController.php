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
use Furniture\FrontendBundle\Repository\RetailerProfileRepository;

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

    /**
     *
     * @var \Furniture\FrontendBundle\Repository\RetailerProfileRepository 
     */
    private $retailerProfileRepository;
            
    function __construct(
    \Twig_Environment $twig, 
            EntityManagerInterface $em, 
            TokenStorageInterface $tokenStorage, 
            UrlGeneratorInterface $urlGenerator, 
            FactoryRepository $factoryRepository,
            RetailerProfileRepository $retailerProfileRepository
    ) {

        $this->twig = $twig;
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->urlGenerator = $urlGenerator;
        $this->factoryRepository = $factoryRepository;
        $this->retailerProfileRepository = $retailerProfileRepository;
    }

    public function map(Request $request)
    {
        
        $mapRetailerMarkers = [];
        
        foreach( ($allRetailers = $this->retailerProfileRepository->findAll()) as $reatilerProfile){
            if($reatilerProfile->getLat() && $reatilerProfile->getLng())
                $mapRetailerMarkers[] = [
                    'id' => $reatilerProfile->getId(),
                    'location' => [
                        'lat' => $reatilerProfile->getLat(),
                        'lng' => $reatilerProfile->getLng(),
                    ],
                    'address' => $reatilerProfile->getAddress(),
                    'name'    => $reatilerProfile->getName(),
                    'emails'  => $reatilerProfile->getEmails() ? implode(',', $reatilerProfile->getEmails()) : '',
                    'phones'  => $reatilerProfile->getPhones() ? implode(',', $reatilerProfile->getPhones()) : '',
                ];
        }
        
        $content = $this->twig->render('FrontendBundle:Factory/Retailer:map.html.twig', [
            'mapRetailerMarkers' => $mapRetailerMarkers,
            'allRetailers' => $allRetailers
            ]);
        return new Response($content);
    }
    
}
