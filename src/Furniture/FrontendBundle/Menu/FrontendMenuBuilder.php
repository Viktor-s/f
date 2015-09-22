<?php

namespace Furniture\FrontendBundle\Menu;

use Furniture\WebBundle\Menu\MenuBuilder;
use Knp\Menu\FactoryInterface;
use Sylius\Component\Rbac\Authorization\AuthorizationCheckerInterface as RbacAuthorizationCheckerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FrontendMenuBuilder extends MenuBuilder
{
    /**
     * Construct
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
        parent::__construct($factory, $securityContext, $translator, $eventDispatcher, $authorizationChecker);
    }

    /**
     * Create a header menu
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createHeaderMenu()
    {
        $menu = $this->factory->createItem('root');

        return $menu;
    }

    /**
     * Create a footer menu
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createFooterMenu()
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('sitemap', [
            'uri' => '#',
            'label' => $this->translator->trans('frontend.menu_items.footer.sitemap')
        ]);

        $menu->addChild('terms_and_condition', [
            'uri' => '#',
            'label' => $this->translator->trans('frontend.menu_items.footer.terms_and_condition')
        ]);

        $menu->addChild('advanced_search', [
            'uri' => '#',
            'label' => $this->translator->trans('frontend.menu_items.footer.advanced_search')
        ]);

        $menu->addChild('privacy_policy', [
            'uri' => '#',
            'label' => $this->translator->trans('frontend.menu_items.footer.privacy_policy')
        ]);

        $menu->addChild('contact_us', [
            'uri' => '#',
            'label' => $this->translator->trans('frontend.menu_items.footer.contact_us')
        ]);

        $menu->addChild('about_us', [
            'uri' => '#',
            'label' => $this->translator->trans('frontend.menu_items.footer.about_us')
        ]);

        return $menu;
    }
}
