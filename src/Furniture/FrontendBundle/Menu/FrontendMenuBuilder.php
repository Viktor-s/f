<?php

namespace Furniture\FrontendBundle\Menu;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FrontendBundle\Repository\ProductSpaceRepository;
use Knp\Menu\FactoryInterface;
use Sylius\Component\Rbac\Authorization\AuthorizationCheckerInterface as RbacAuthorizationCheckerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface as SymfonyAuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\RequestStack;

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
     * @var TokenStorageInterface
     */
    private $tokenStorage;
    
    /**
     * @var ProductSpaceRepository
     */
    private $productSpaceRepository;
    
    /**
     *
     * @var \Symfony\Component\HttpFoundation\RequestStack
     */
    private $requestStack;
    
    /**
     * Construct
     *
     * @param FactoryInterface                     $factory
     * @param TranslatorInterface                  $translator
     * @param SymfonyAuthorizationCheckerInterface $sfAuthorizationChecker
     * @param RbacAuthorizationCheckerInterface    $rbacAuthorizationChecker
     * @param UrlGeneratorInterface                $urlGenerator
     * @param TokenStorageInterface                $tokenStorage
     * @param ProductSpaceRepository               $productSpaceRepository
     * @param RequestStack $requestStack
     */
    public function __construct(
        FactoryInterface $factory,
        TranslatorInterface $translator,
        SymfonyAuthorizationCheckerInterface $sfAuthorizationChecker,
        RbacAuthorizationCheckerInterface $rbacAuthorizationChecker,
        UrlGeneratorInterface $urlGenerator,
        TokenStorageInterface $tokenStorage,
        ProductSpaceRepository $productSpaceRepository,
        RequestStack $requestStack
    ) {
        $this->factory = $factory;
        $this->translator = $translator;
        $this->sfAuthorizationChecker = $sfAuthorizationChecker;
        $this->rbacAuthorizationChecker = $rbacAuthorizationChecker;
        $this->urlGenerator = $urlGenerator;
        $this->tokenStorage = $tokenStorage;
        $this->productSpaceRepository = $productSpaceRepository;
        $this->requestStack = $requestStack;
    }

    /**
     * Create a header menu
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createReatilerHeaderMenu()
    {
        $menu = $this->factory->createItem('root');
        /** @var \Furniture\UserBundle\Entity\User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        if (!$user || !$user->isRetailer()) {
            return $menu;
        }

        $isDemo = $user
                ->getRetailerUserProfile()
                ->getRetailerProfile()
                ->isDemo();

        $menu->addChild('home', [
            'route' => 'homepage',
            'label' => $this->translator->trans('frontend.menu_items.header.homepage'),
            'display' => !$isDemo,
        ]);

//        $menu->addChild('factories', [
//            'uri' => $this->urlGenerator->generate('factory_side_list'),
//            'label' => $this->translator->trans('frontend.menu_items.header.factories'),
//        ]);

        $menu->addChild('products', [
            'uri' => $this->urlGenerator->generate('products', []),
            'label' => $this->translator->trans('frontend.menu_items.header.products')
        ]);

        $menu->addChild('specifications', [
            'uri' => $this->urlGenerator->generate('specifications'),
            'label' => $this->translator->trans('frontend.menu_items.header.specifications')
        ]);

        $menu->addChild('buyers', [
            'uri' => $this->urlGenerator->generate('specification_buyers'),
            'label' => $this->translator->trans('frontend.menu_items.header.specification_buyers')
        ]);

        switch ($this->requestStack->getMasterRequest()->get('_route')) {
            case 'homepage':
                $menu->getChild('home')->setCurrent('true');
                break;

            case 'factory_side_list':
                $menu->getChild('factories')->setCurrent('true');
                break;

            case 'catalog':
                $menu->getChild('products')->setCurrent('true');
                break;

            case 'products':
                $menu->getChild('products')->setCurrent('true');
                break;

            case 'specifications':
                $menu->getChild('specifications')->setCurrent('true');
                break;

            case 'specification_buyers':
                $menu->getChild('buyers')->setCurrent('true');
                break;
        }


        return $menu;
    }

    /**
     * Create a header menu
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createFactoryHeaderMenu() {
        $menu = $this->factory->createItem('root');
        /** @var \Furniture\UserBundle\Entity\User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        if (!$user || !$user->isFactory()) {
            return $menu;
        }

        $menu->addChild('home', [
            'uri' => $this->urlGenerator->generate('factory'),
            'label' => $this->translator->trans('frontend.menu_items.header.factory.statistics')
        ]);
        
        $menu->addChild('map', [
            'uri' => $this->urlGenerator->generate('retailer_map'),
            'label' => $this->translator->trans('frontend.menu_items.header.factory.map')
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
            'label' => $this->translator->trans('frontend.menu_items.footer.sitemap'),
            'display' => false,
        ]);

        $menu->addChild('terms_and_condition', [
            'uri' => '#',
            'label' => $this->translator->trans('frontend.menu_items.footer.terms_and_condition')
        ]);

        $menu->addChild('advanced_search', [
            'uri' => '#',
            'label' => $this->translator->trans('frontend.menu_items.footer.advanced_search'),
            'display' => false,
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

        $menu->addChild('user_relations', [
            'uri' => $this->urlGenerator->generate('factory_profile_retailer_relations'),
            'label' => $this->translator->trans('frontend.retailer_relations')
        ]);

        $menu->addChild('default_relation', [
            'uri' => $this->urlGenerator->generate('factory_profile_default_relation'),
            'label' => $this->translator->trans('frontend.default_relation')
        ]);

        return $menu;
    }

    /**
     * Create menu for retailer profile
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createRetailerAdminProfileMenu()
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('factory_partners', [
            'uri' => $this->urlGenerator->generate('retailer_profile_partners'),
            'label' => $this->translator->trans('frontend.factory_partners'),
            'display' => $this->sfAuthorizationChecker->isGranted('RETAILER_PARTNERS_LIST'),
        ]);

        $menu->addChild('factory_rates', [
            'uri'     => $this->urlGenerator->generate('retailer_profile_factory_rates'),
            'label'   => $this->translator->trans('frontend.factory_rates'),
            'display' => $this->sfAuthorizationChecker->isGranted('RETAILER_FACTORY_RATE_LIST'),
        ]);

        $menu->addChild('employees', [
            'uri'     => $this->urlGenerator->generate('retailer_profile_employees'),
            'label'   => $this->translator->trans('frontend.managers'),
            'display' => $this->sfAuthorizationChecker->isGranted('RETAILER_EMPLOYEE_LIST'),
        ]);

        return $menu;
    }
    
    /**
     * Create menu for retailer profile
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createUserProfileMenu()
    {
        /** @var \Furniture\UserBundle\Entity\User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $menu = $this->factory->createItem('root');

        $menu->addChild('user_info_update', [
            'uri' => $this->urlGenerator->generate('user_profile_update'),
            'label' => $this->translator->trans('frontend.user_profile_side.update')
        ]);

        $menu->addChild('user_password_update', [
            'uri' => $this->urlGenerator->generate('user_password_update'),
            'label' => $this->translator->trans('frontend.user_profile_side.password_upadate')
        ]);

        return $menu;
    }
    
    /**
     * Create menu for factory side page
     *
     * @param Factory $factory
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createFactorySideMenu(Factory $factory)
    {
        $menu = $this->factory->createItem('root');
        
        $menu->addChild('general', [
            'uri' => $this->urlGenerator->generate('retailer_profile_partners_general', ['factory' => $factory->getId()]),
            'label' => $this->translator->trans('frontend.factory_side.menu.general')
        ]);

        $menu->addChild('news', [
            'uri' => $this->urlGenerator->generate('retailer_profile_partners_news', ['factory' => $factory->getId()]),
            'label' => $this->translator->trans('frontend.factory_side.menu.news')
        ]);

//        if ($this->sfAuthorizationChecker->isGranted('VIEW_PRODUCTS', $factory)) {
//            $menu->addChild('collections', [
//                'uri'   => $this->urlGenerator->generate('factory_side_collections', ['factory' => $factory->getId()]),
//                'label' => $this->translator->trans('frontend.factory_side.menu.collections')
//            ]);
//        }

        // Check active state for factory retailer relation.
//        if ($this->sfAuthorizationChecker->isGranted('ACTIVE_RELATION', $factory)) {
//            $menu->addChild('work_info', [
//                'uri' => $this->urlGenerator->generate('factory_side_work_info', ['factory' => $factory->getId()]),
//                'label' => $this->translator->trans('frontend.factory_side.menu.sales_conditions')
//            ]);
//        }
        
        $menu->addChild('contacts', [
            'uri' => $this->urlGenerator->generate('retailer_profile_partners_contacts', ['factory' => $factory->getId()]),
            'label' => $this->translator->trans('frontend.factory_side.menu.contacts')
        ]);
        // Check active state for factory retailer relation.
//        if ($this->sfAuthorizationChecker->isGranted('ACTIVE_RELATION', $factory)) {
//            $menu->addChild('circulars', [
//                'uri' => $this->urlGenerator->generate('retailer_profile_partners_circulars', ['factory' => $factory->getId()]),
//                'label' => $this->translator->trans('frontend.factory_side.menu.circulars')
//            ]);
//        }

        $setActiveForMenuItem = function ($item) use ($menu)
        {
            if ($child = $menu->getChild($item)) {
                $child->setCurrent(true);
            }
        };
        
        switch($this->requestStack->getMasterRequest()->get('_route')){
            case 'retailer_profile_partners_general':
                $setActiveForMenuItem('general');
                break;

            case 'retailer_profile_partners_news':
            case 'retailer_profile_partners_news_view':
                $setActiveForMenuItem('news');
                break;

            case 'retailer_profile_partners_collections':
                $setActiveForMenuItem('collections');
                break;

            case 'retailer_profile_partners_work_info':
                $setActiveForMenuItem('work_info');
                break;

            case 'retailer_profile_partners_contacts':
                $setActiveForMenuItem('contacts');
                break;

            case 'retailer_profile_partners_circulars':
            case 'retailer_profile_partners_circular':
                $setActiveForMenuItem('circulars');
                break;
                
        }

        return $menu;
    }

    /**
     * Create menu for user profile
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createProfileHeaderMenu()
    {
        $menu = $this->factory->createItem('root');

        $menu->addChild('user_profile', [
            'route' => 'user_profile',
            'label' => '<i class="fa fa-user"></i><span>'.$this->translator->trans('frontend.button.user_profile').'</span>',
        ])->setAttribute('class', 'header-functionality-entry')->setExtra('safe_label', true);

        /** @var \Furniture\UserBundle\Entity\User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        if ($user->isRetailer() && ($user->getRetailerUserProfile()->isRetailerAdmin()
            || $user->getRetailerUserProfile()->isRetailerEmployee())
        ) {
            $menu->addChild('retailer_profile', [
                'route' => 'retailer_profile',
                'label' => '<i class="fa fa-cog"></i><span>'.$this->translator->trans('frontend.button.settings').'</span>',
            ])->setAttribute('class', 'header-functionality-entry')->setExtra('safe_label', true);
        } else if ($this->sfAuthorizationChecker->isGranted('ROLE_FACTORY_ADMIN')
            || $this->sfAuthorizationChecker->isGranted('ROLE_FACTORY_USER')
        ) {
            $menu->addChild('factory_profile', [
                'route' => 'factory_profile',
                'label' => '<i class="fa fa-cog"></i><span>'.$this->translator->trans('frontend.button.settings').'</span>',
            ])->setAttribute('class', 'header-functionality-entry')->setExtra('safe_label', true);
        }

        $menu->addChild('logout', [
            'route' => 'security_logout',
            'label' => '<i class="fa fa-sign-in"></i><span>'.$this->translator->trans('frontend.button.logout').'</span>',
        ])->setAttribute('class', 'header-functionality-entry')->setExtra('safe_label', true);

        return $menu;
    }
}
