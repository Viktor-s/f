<?php

namespace Furniture\FrontendBundle\Menu;

use Knp\Menu\FactoryInterface;
use Sylius\Component\Rbac\Authorization\AuthorizationCheckerInterface as RbacAuthorizationCheckerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface as SymfonyAuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class FrontendMenuBuilder
{
    /**
     * @var FactoryInterface
     */
    private $factory;

    /**
     * @var SymfonyAuthorizationCheckerInterface
     */
    private $sfAuthorizationChecker;

    /**
     * @var RbacAuthorizationCheckerInterface
     */
    private $rbacAuthorizationChecker;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * Construct
     *
     * @param FactoryInterface                     $factory
     * @param TranslatorInterface                  $translator
     * @param SymfonyAuthorizationCheckerInterface $sfAuthorizationChecker
     * @param RbacAuthorizationCheckerInterface    $rbacAuthorizationChecker
     * @param UrlGeneratorInterface                $urlGenerator
     */
    public function __construct(
        FactoryInterface $factory,
        TranslatorInterface $translator,
        SymfonyAuthorizationCheckerInterface $sfAuthorizationChecker,
        RbacAuthorizationCheckerInterface $rbacAuthorizationChecker,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->factory = $factory;
        $this->translator = $translator;
        $this->sfAuthorizationChecker = $sfAuthorizationChecker;
        $this->rbacAuthorizationChecker = $rbacAuthorizationChecker;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Create a header menu
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createHeaderMenu()
    {
        $menu = $this->factory->createItem('root');

        $menu
            ->addChild('home', [
                'route' => 'homepage',
                'label' => $this->translator->trans('frontend.menu_items.header.homepage')
            ]);

        $factories = $menu->addChild('factories', [
            'uri' => '#',
            'label' => $this->translator->trans('frontend.menu_items.header.factories')
        ]);
        
        $products = $menu->addChild('products', [
            'uri' => '#',
            'label' => $this->translator->trans('frontend.menu_items.header.products')
        ]);
        
        $catalog = $products->addChild('catalog', [
            'uri' => $this->urlGenerator->generate('taxonomy'),
            'label' => $this->translator->trans('frontend.menu_items.header.catalog')
        ]);
        
        $catalog = $products->addChild('Compositions', [
            'uri' => 'compositions',
            'label' => $this->translator->trans('frontend.menu_items.header.compositions')
        ]);

        $factories->addChild('factories', [
            'uri' => '#',
            'label' => $this->translator->trans('frontend.menu_items.header.factories')
        ]);

        $specifications = $menu->addChild('specifications', [
            'uri' => $this->urlGenerator->generate('specifications'),
            'label' => $this->translator->trans('frontend.menu_items.header.specifications')
        ]);

        $specifications->addChild('buyers', [
            'uri' => $this->urlGenerator->generate('specification_buyers'),
            'label' => $this->translator->trans('frontend.menu_items.header.specification_buyers')
        ]);

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

    /**
     * Create menu for factory profile
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createFactoryProfileMenu()
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('item1', [
            'uri' => '#',
            'label' => 'Item #1'
        ]);

        $menu->addChild('item2', [
            'uri' => '#',
            'label' => 'Item #2'
        ]);

        return $menu;
    }

    /**
     * Create menu for factory profile
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createContentUserProfileMenu()
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('item1', [
            'uri' => '#',
            'label' => 'Item #1'
        ]);

        $menu->addChild('item2', [
            'uri' => '#',
            'label' => 'Item #2'
        ]);

        return $menu;
    }
}
