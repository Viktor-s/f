<?php

namespace Furniture\UserBundle\Security\EmailVerifier;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\MailerBundle\Mailer\Mailer;
use Furniture\UserBundle\Entity\User;

class EmailVerifier
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
     * Send verify email link to user.
     *
     * @param User      $user
     * @param bool|true $update
     *
     * @return User
     */
    public function verifyEmail(User $user)
    {
        $verifyToken = $user->requestForVerifyEmail();

        $email = $user->getCustomer()->getEmail();
        $name = $user->getCustomer()->getFullName();
        $parameters = [
            'username' => $name,
            'token'    => $verifyToken,
        ];

        $this->mailer->send($email,
            'Verify your email',
            'UserBundle:Mail:verify_email_request.html.twig',
            $parameters,
            $name
        );
    }
}
