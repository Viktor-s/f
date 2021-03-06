<?php

namespace Furniture\UserBundle\Security\Authentication;

use Furniture\UserBundle\PasswordResetter\PasswordResetter;
use Furniture\UserBundle\Security\Exception\AuthenticationNeedResetPasswordException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler as BaseDefaultAuthenticationFailureHandler;
use Symfony\Component\Security\Http\HttpUtils;

class DefaultAuthenticationFailureHandler extends BaseDefaultAuthenticationFailureHandler
{
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var PasswordResetter
     */
    private $passwordResetter;

    /**
     * Constructor.
     *
     * @param HttpKernelInterface   $httpKernel
     * @param HttpUtils             $httpUtils
     * @param array                 $options
     * @param LoggerInterface       $logger
     * @param UrlGeneratorInterface $urlGenerator
     * @param PasswordResetter      $passwordResetter
     */
    public function __construct(
        HttpKernelInterface $httpKernel,
        HttpUtils $httpUtils,
        array $options,
        LoggerInterface $logger,
        UrlGeneratorInterface $urlGenerator,
        PasswordResetter $passwordResetter
    )
    {
        parent::__construct($httpKernel, $httpUtils, $options, $logger);

        $this->urlGenerator = $urlGenerator;
        $this->passwordResetter = $passwordResetter;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($exception instanceof AuthenticationNeedResetPasswordException) {
            $user = $exception->getUser();
            if ($user->isNeedResetPassword()
                && empty($user->getConfirmationToken())
            ) {
                $this->passwordResetter->resetPassword($user);
            }

            $token = $user->getConfirmationToken();
            $toUrl = $this->urlGenerator->generate('security_need_reset_password');
            $session = $request->getSession();
            $session->set('reset-password-token', $token);

            return new RedirectResponse($toUrl);
        }

        return parent::onAuthenticationFailure($request, $exception);
    }
}
