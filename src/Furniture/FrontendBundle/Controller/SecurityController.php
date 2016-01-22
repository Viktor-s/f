<?php

namespace Furniture\FrontendBundle\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

class SecurityController
{
    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * Construct
     *
     * @param \Twig_Environment         $twig
     * @param CsrfTokenManagerInterface $csrfTokenManager
     */
    public function __construct(\Twig_Environment $twig, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->csrfTokenManager = $csrfTokenManager;
        $this->twig = $twig;
    }

    /**
     * Login action
     *
     * @param Request $request
     *
     * @return Response
     */
    public function login(Request $request)
    {
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();

        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }
        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }
        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

        $csrfToken = $this->csrfTokenManager
            ->getToken('authenticate')
            ->getValue();

        $content = $this->twig->render('FrontendBundle:Security:login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
            'csrf_token' => $csrfToken
        ]);

        return new Response($content);
    }

    /**
     * Reset password
     *
     * @param Request $request
     */
    public function resetPassword(Request $request)
    {
    }
}
