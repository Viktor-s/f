<?php

namespace Furniture\WebBundle\Menu;

use JMS\TranslationBundle\Annotation\Ignore;
use Knp\Menu\FactoryInterface;
use Sylius\Component\Rbac\Authorization\AuthorizationCheckerInterface as RbacAuthorizationCheckerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Abstract menu builder.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
abstract class MenuBuilder
{
    /**
     * Menu factory.
     *
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * Security context.
     *
     * @var SecurityContextInterface
     */
    protected $securityContext;

    /**
     * Translator instance.
     *
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * Request.
     *
     * @var Request
     */
    protected $request;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var RbacAuthorizationCheckerInterface
     */
    protected $authorizationChecker;

    /**
     * Constructor.
     *
     * @param FactoryInterface                  $factory
     * @param SecurityContextInterface          $securityContext
     * @param TranslatorInterface               $translator
     * @param EventDispatcherInterface          $eventDispatcher
     * @param RbacAuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
        FactoryInterface $factory,
        SecurityContextInterface $securityContext,
        TranslatorInterface $translator,
        EventDispatcherInterface $eventDispatcher,
        RbacAuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->factory = $factory;
        $this->securityContext = $securityContext;
        $this->translator = $translator;
        $this->eventDispatcher = $eventDispatcher;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Sets the request the service
     *
     * @param Request $request
     */
    public function setRequest(Request $request = null)
    {
        $this->request = $request;
    }

    /**
     * Translate label.
     *
     * @param string $label
     * @param array  $parameters
     *
     * @return string
     */
    protected function translate($label, $parameters = array())
    {
        return $this->translator->trans(/** @Ignore */ $label, $parameters, 'menu');
    }
}
