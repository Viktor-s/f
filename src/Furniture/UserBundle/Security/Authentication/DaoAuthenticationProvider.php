<?php

namespace Furniture\UserBundle\Security\Authentication;

use Furniture\UserBundle\Entity\User;
use Furniture\UserBundle\Security\Exception\AuthenticationNeedResetPasswordException;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider as BaseDaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class DaoAuthenticationProvider extends BaseDaoAuthenticationProvider
{
    /**
     * {@inheritdoc}
     */
    protected function retrieveUser($username, UsernamePasswordToken $token)
    {
        $user = parent::retrieveUser($username, $token);

        if ($user instanceof User) {
            if ($user->isNeedResetPassword()) {
                throw new AuthenticationNeedResetPasswordException($user, 'Need reset password.');
            }
        }

        return $user;
    }
}
