<?php

namespace Furniture\FrontendBundle\Controller\Profile\Factory;

use Sylius\Component\Rbac\Authorization\AuthorizationCheckerInterface as RbacAuthorizationChecker;
use Symfony\Component\HttpFoundation\Response;

class DashboardController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * Construct
     *
     * @param \Twig_Environment        $twig
     */
    public function __construct(
        \Twig_Environment $twig
    ) {
        $this->twig = $twig;
    }

    /**
     * Dashboard action
     */
    public function dashboard()
    {
        $content = $this->twig->render('FrontendBundle:Profile/Factory:dashboard.html.twig', [
        ]);

        return new Response($content);
    }
}
