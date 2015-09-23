<?php

namespace Furniture\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HomepageController
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
     * Construct
     *
     * @param \Twig_Environment     $twig
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(\Twig_Environment $twig, UrlGeneratorInterface $urlGenerator)
    {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Index action
     *
     * @return Response
     */
    public function index()
    {
        $url = $this->urlGenerator->generate('homepage', [
            'locale' => 'en'
        ]);

        return new RedirectResponse($url);
    }

    /**
     * Home page action
     *
     * @return Response
     */
    public function home()
    {
        $content = $this->twig->render('FrontendBundle::homepage.html.twig', [
        ]);

        return new Response($content);
    }
}
