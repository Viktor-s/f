<?php

namespace Furniture\UserBundle\PasswordResetter;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\MailerBundle\Mailer\Mailer;
use Furniture\UserBundle\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PasswordResetter
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * Construct
     *
     * @param EntityManagerInterface $em
     * @param Mailer                 $mailer
     */
    public function __construct(EntityManagerInterface $em, Mailer $mailer)
    {
        $this->em = $em;
        $this->mailer = $mailer;
    }

    /**
     * Reset password for user
     *
     * @param User $user
     */
    public function resetPassword(User $user)
    {
        $token = $user->requestForResetPassword();

        $email = $user->getCustomer()->getEmail();
        $name = $user->getCustomer()->getFullName();
        $parameters = [
            'username' => $name,
            'token' => $token
        ];

        $this->mailer->send($email, 'Reset password', 'UserBundle:Mail:reset_password_request.html.twig', $parameters, $name);

        $this->em->flush($user);
    }
}
