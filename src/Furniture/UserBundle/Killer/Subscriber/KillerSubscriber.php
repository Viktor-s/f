<?php

namespace Furniture\UserBundle\Killer\Subscriber;

use Furniture\UserBundle\Killer\Killer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class KillerSubscriber implements EventSubscriberInterface
{
    /**
     * @var Killer
     */
    private $killer;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * Construct
     *
     * @param Killer                $killer
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(Killer $killer, UrlGeneratorInterface $urlGenerator)
    {
        $this->killer = $killer;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Kill user if necessary
     *
     * @param GetResponseEvent $event
     */
    public function onRequestForKill(GetResponseEvent $event)
    {
        if ($this->killer->isShouldKill()) {
            $this->killer->kill();

            $loginUrl = $this->urlGenerator->generate('security_login');
            $response = new RedirectResponse($loginUrl);
            $event->setResponse($response);
        }
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['onRequestForKill', 8],
            ],
        ];
    }
}
