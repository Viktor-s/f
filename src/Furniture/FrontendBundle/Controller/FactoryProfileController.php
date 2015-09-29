<?php

namespace Furniture\FrontendBundle\Controller;

use Sylius\Component\Rbac\Authorization\AuthorizationCheckerInterface as RbacAuthorizationChecker;
use Symfony\Component\HttpFoundation\Response;

class FactoryProfileController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var RbacAuthorizationChecker
     */
    private $rbacAuthorizationChecker;

    /**
     * Construct
     *
     * @param \Twig_Environment        $twig
     * @param RbacAuthorizationChecker $rbacAuthorizationChecker
     */
    public function __construct(
        \Twig_Environment $twig,
        RbacAuthorizationChecker $rbacAuthorizationChecker
    ) {
        $this->twig = $twig;
        $this->rbacAuthorizationChecker = $rbacAuthorizationChecker;
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
