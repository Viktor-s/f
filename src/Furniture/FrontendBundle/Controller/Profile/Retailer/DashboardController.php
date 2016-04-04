<?php

namespace Furniture\FrontendBundle\Controller\Profile\Retailer;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * Construct
     *
     * @param \Twig_Environment             $twig
     * @param TokenStorageInterface         $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        \Twig_Environment $twig,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->twig = $twig;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Dashboard action
     */
    public function dashboard()
    {
        /** @var \Furniture\UserBundle\Entity\User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        $retailer = $user->getRetailerUserProfile()->getRetailerProfile();

        if (!$this->authorizationChecker->isGranted('RETAILER_VIEW', $retailer)) {
            throw new AccessDeniedException();
        }

        $content = $this->twig->render(
            'FrontendBundle:Profile/Retailer:dashboard.html.twig',
            [
                'retailer' => $retailer,
            ]
        );

        return new Response($content);
    }
}
