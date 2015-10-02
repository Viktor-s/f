<?php

namespace Furniture\FrontendBundle\Controller\Profile\ContentUser;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DashboardController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Construct
     *
     * @param \Twig_Environment          $twig
     * @param TokenStorageInterface      $tokenStorage
     */
    public function __construct(
        \Twig_Environment $twig,
        TokenStorageInterface $tokenStorage
    ) {
        $this->twig = $twig;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Dashboard action
     */
    public function dashboard()
    {
        $content = $this->twig->render('FrontendBundle:Profile/ContentUser:dashboard.html.twig', [
        ]);

        return new Response($content);
    }
}
