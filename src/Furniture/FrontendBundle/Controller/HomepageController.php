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
     * @var array
     */
    private $availableLocales;

    /**
     * Construct
     *
     * @param \Twig_Environment     $twig
     * @param UrlGeneratorInterface $urlGenerator
     * @param string                $availableLocales
     */
    public function __construct(\Twig_Environment $twig, UrlGeneratorInterface $urlGenerator, $availableLocales)
    {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->availableLocales = explode('|', $availableLocales);
    }

    /**
     * Index action
     *
     * @return Response
     */
    public function index()
    {
        return new RedirectResponse('/en/');
//        $content = $this->twig->render('FrontendBundle::select_locale.html.twig', [
//            'available_locales' => $this->availableLocales
//        ]);
//
//        return new Response($content);
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
