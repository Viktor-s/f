<?php

namespace Furniture\UserBundle\Killer\Subscriber;

use Furniture\UserBundle\Killer\Killer;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CollectSessionSubscriber implements EventSubscriberInterface
{
    /**
     * @var Killer
     */
    private $killer;

    /**
     * Construct
     *
     * @param Killer                $killer
     */
    public function __construct(Killer $killer)
    {
        $this->killer = $killer;
    }

    /**
     * Kill user if necessary
     *
     * @param GetResponseEvent $event
     */
    public function collectSession(GetResponseEvent $event)
    {
        if ($event->getRequestType() !== HttpKernelInterface::MASTER_REQUEST) {
            return;
        }

        $request = $event->getRequest();
        $session = $request->getSession();

        $id = $session->getId();
        $this->killer->collectionSessionId($id);
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => [
                ['collectSession', 0],
            ],
        ];
    }
}
