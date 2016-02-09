<?php

namespace Furniture\UserBundle\Security\Exception;

use Furniture\UserBundle\Entity\User;
use Symfony\Component\Security\Core\Exception\AuthenticationException;

class AuthenticationNeedResetPasswordException extends AuthenticationException
{
    /**
     * @var User
     */
    private $user;

    /**
     * Construct
     *
     * @param User   $user
     * @param string $message
     */
    public function __construct(User $user, $message)
    {
        $this->user = $user;

        parent::__construct($message);
    }

    /**
     * Get user
     *
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }
}
