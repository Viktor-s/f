<?php

namespace Furniture\FrontendBundle\Controller\Profile\Factory;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserMapController
{
    
    /**
     * @var \Twig_Environment
     */
    private $twig;
    
    /**
     * @var EntityManagerInterface
     */
    private $em;
    
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    
    public function __construct(
        \Twig_Environment $twig,
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage
            ){
        $this->twig = $twig;
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    public function clusterMap()
    {
        $content = $this->twig->render('FrontendBundle:Profile/Factory/DefaultRelation:edit.html.twig', [
            'form' => $form->createView()
        ]);

        return new Response($content);
    }
    
}

