<?php

namespace Furniture\UserBundle\Security\Authentication;

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
     * Constructor.
     *
     * @param HttpKernelInterface   $httpKernel
     * @param HttpUtils             $httpUtils
     * @param array                 $options
     * @param LoggerInterface       $logger
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        HttpKernelInterface $httpKernel,
        HttpUtils $httpUtils,
        array $options,
        LoggerInterface $logger,
        UrlGeneratorInterface $urlGenerator
    )
    {
        parent::__construct($httpKernel, $httpUtils, $options, $logger);

        $this->urlGenerator = $urlGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        if ($exception instanceof AuthenticationNeedResetPasswordException) {
            $toUrl = $this->urlGenerator->generate('security_reset_password_request', [
                'reason' => 'nrp'
            ]);

            return new RedirectResponse($toUrl);
        }

        return parent::onAuthenticationFailure($request, $exception);
    }
}
