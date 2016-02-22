<?php

namespace Furniture\UserBundle\Security\EmailVerifier;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\MailerBundle\Mailer\Mailer;
use Furniture\UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class EmailVerifier implements EventSubscriberInterface
{
    /**
     * @var Mailer
     */
    private $mailer;

    /**
     * @var array
     */
    private $mails = [];

    /**
     * Construct
     *
     * @param Mailer $mailer
     */
    public function __construct(Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Send verify email link to user.
     *
     * @param User $user
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

        $this->mails[] = [
            'email'      => $email,
            'parameters' => $parameters,
            'name'       => $name,
        ];
    }

    /**
     * Flush verifier
     */
    public function flush()
    {
        foreach ($this->mails as $mailInfo) {
            $this->mailer->send(
                $mailInfo['email'],
                'Verify you email',
                'UserBundle:Mail:verify_email_request.html.twig',
                $mailInfo['parameters'],
                $mailInfo['name']
            );
        }

        $this->mails = [];
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::TERMINATE => [
                ['flush', 0]
            ]
        ];
    }
}
