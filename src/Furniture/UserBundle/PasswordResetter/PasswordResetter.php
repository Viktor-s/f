<?php

namespace Furniture\UserBundle\PasswordResetter;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\UserBundle\Entity\User;

class PasswordResetter
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Construct
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Reset password for user
     *
     * @param User $user
     */
    public function resetPassword(User $user)
    {
        $user->requestForResetPassword();

        // @todo: send mail

        $this->em->flush($user);
    }

    /**
     * Generate security hash for reset password
     *
     * @return string
     */
    private function generateSecurityHashForResetPassword()
    {
    }
}
