<?php

namespace Furniture\UserBundle\Security\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\RememberMe\TokenBasedRememberMeServices;
use Furniture\UserBundle\Entity\User;

class DefaultTokenBasedRememberMeServices extends TokenBasedRememberMeServices
{
    /**
     * {@inheritdoc}
     */
    protected function processAutoLoginCookie(array $cookieParts, Request $request)
    {
        try {
            $user = parent::processAutoLoginCookie($cookieParts, $request);
        } catch (\Exception $e) {
            throw $e;
        }

        // Check that user should reset password.
        if ($user instanceof User && $user->isNeedResetPassword()) {
            throw new AuthenticationException('User need reset password.');
        }

        return $user;
    }
}
