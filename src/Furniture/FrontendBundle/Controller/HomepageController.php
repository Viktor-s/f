<?php

namespace Furniture\FrontendBundle\Controller;

use Furniture\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
     *
     * @var \Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Construct
     *
     * @param \Twig_Environment     $twig
     * @param UrlGeneratorInterface $urlGenerator
     * @param string                $availableLocales
     */
    public function __construct(
            \Twig_Environment $twig, 
            UrlGeneratorInterface $urlGenerator, 
            $availableLocales, 
            TokenStorageInterface $tokenStorage
    )
    {
        $this->twig = $twig;
        $this->urlGenerator = $urlGenerator;
        $this->availableLocales = explode('|', $availableLocales);
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Index action
     *
     * @return Response
     */
    public function index()
    {
        return new RedirectResponse('/en/');
    }

    /**
     * Home page action
     *
     * @return Response
     */
    public function home()
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        if(!$user)
            throw new NotFoundHttpException('Logged in user not found!');
        
        if($user->isFactory()) {
            return new RedirectResponse($this->urlGenerator->generate('factory'));
        }

        if ($user->isRetailer()
            && $user->getRetailerUserProfile()->getRetailerProfile()->isDemo()
        ) {
            return new RedirectResponse($this->urlGenerator->generate('products'));
        }

        $content = $this->twig->render('FrontendBundle::homepage.html.twig', []);
        
        return new Response($content);
    }
}
