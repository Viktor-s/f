<?php

namespace Furniture\FrontendBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SearchController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Construct
     *
     * @param \Twig_Environment      $twig
     * @param UrlGeneratorInterface  $urlGenerator
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface  $tokenStorage
     */
    public function __construct(
        \Twig_Environment $twig,
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->twig = $twig;
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
    }

    public function index(Request $request)
    {
        $content = $this->twig->render('FrontendBundle:Search:search_results.html.twig', []);

        return new Response($content);
    }
}
