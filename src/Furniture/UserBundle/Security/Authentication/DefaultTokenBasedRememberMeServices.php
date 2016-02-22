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

        if ($user instanceof User) {
            // Check that user should reset password.
            if ($user->isNeedResetPassword()) {
                throw new AuthenticationException('User need reset password.');
            }

            // Check that user should reset password.
            if ($user->getVerifyEmailHash()) {
                throw new AuthenticationException('User need verify email.');
            }
        }

        return $user;
    }
}
