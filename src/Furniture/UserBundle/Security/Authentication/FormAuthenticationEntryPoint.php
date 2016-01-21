<?php

namespace Furniture\UserBundle\Security\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\FormAuthenticationEntryPoint as BaseEntryPoint;

/**
 * Override base form authentication entry point for control Ajax requests
 */
class FormAuthenticationEntryPoint extends BaseEntryPoint
{
    /**
     * {@inheritdoc}
     */
    public function start(Request $request, AuthenticationException $authException = null)
    {
        if ($request->isXmlHttpRequest()) {
            return new Response('', 401);
        }

        return parent::start($request, $authException);
    }
}
