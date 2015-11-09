<?php

namespace Furniture\FrontendBundle\Controller\Profile\Retailer;

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
        /** @var \Furniture\CommonBundle\Entity\User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $retailer = $user->getRetailerProfile();

        $content = $this->twig->render('FrontendBundle:Profile/Retailer:dashboard.html.twig', [
            'retailer' => $retailer
        ]);

        return new Response($content);
    }
}
