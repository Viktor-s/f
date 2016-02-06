<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Furniture\UserBundle\Security\Authentication;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\RememberMe\TokenBasedRememberMeServices;
use Furniture\UserBundle\Entity\User;

/**
 * Concrete implementation of the RememberMeServicesInterface providing
 * remember-me capabilities without requiring a TokenProvider.
 *
 * @author Johannes M. Schmitt <schmittjoh@gmail.com>
 */
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
