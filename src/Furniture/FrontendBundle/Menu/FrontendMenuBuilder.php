<?php

namespace Furniture\FrontendBundle\Menu;

use Furniture\FactoryBundle\Entity\Factory;
use Knp\Menu\FactoryInterface;
use Sylius\Component\Rbac\Authorization\AuthorizationCheckerInterface as RbacAuthorizationCheckerInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface as SymfonyAuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonomyRepository;
use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonRepository;

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
     *
     * @var TaxonRepository
     */
    private $taxonRepository;

    /**
     * @var TaxonomyRepository
     */
    private $taxonomyRepository;
    
    /**
     * Construct
     *
     * @param FactoryInterface                     $factory
     * @param TranslatorInterface                  $translator
     * @param SymfonyAuthorizationCheckerInterface $sfAuthorizationChecker
     * @param RbacAuthorizationCheckerInterface    $rbacAuthorizationChecker
     * @param UrlGeneratorInterface                $urlGenerator
     * @param TokenStorageInterface                $tokenStorage
     * @param TaxonRepository                      $taxonRepository
     * @param TaxonomyRepository                   $taxonomyRepository
     */
    public function __construct(
        FactoryInterface $factory,
        TranslatorInterface $translator,
        SymfonyAuthorizationCheckerInterface $sfAuthorizationChecker,
        RbacAuthorizationCheckerInterface $rbacAuthorizationChecker,
        UrlGeneratorInterface $urlGenerator,
        TokenStorageInterface $tokenStorage,
        TaxonRepository $taxonRepository,
        TaxonomyRepository $taxonomyRepository
    ) {
        $this->factory = $factory;
        $this->translator = $translator;
        $this->sfAuthorizationChecker = $sfAuthorizationChecker;
        $this->rbacAuthorizationChecker = $rbacAuthorizationChecker;
        $this->urlGenerator = $urlGenerator;
        $this->tokenStorage = $tokenStorage;
        $this->taxonRepository = $taxonRepository;
        $this->taxonomyRepository = $taxonomyRepository;
    }

    /**
     * Create a header menu
     *
     * @return \Knp\Menu\ItemInterface
     */
    public function createHeaderMenu()
    {
        $menu = $this->factory->createItem('root');
        $user = $this->tokenStorage->getToken()->getUser();

        if (!$user) {
            return $menu;
        }
        
        $menu
            ->addChild('home', [
                'route' => 'homepage',
                'label' => $this->translator->trans('frontend.menu_items.header.homepage')
            ]);

        if($user->isContentUser()) {
            $factories = $menu->addChild('factories', [
                'uri' => $this->urlGenerator->generate('factory_side_list'),
                'label' => $this->translator->trans('frontend.menu_items.header.factories')
            ]);
        }
        
        $products = $menu->addChild('products', [
            'uri' => $this->urlGenerator->generate('taxonomy'),
            'label' => $this->translator->trans('frontend.menu_items.header.products')
        ]);

        /** @var \Sylius\Component\Core\Model\Taxon[] $taxons */
        $taxons = $this->taxonomyRepository
            ->findOneBy(['name' => 'Category'])
            ->getTaxons();
        
        foreach ($taxons as $taxon){
            $products->addChild('taxon_'.$taxon->getName(), [
                'uri' => $this->urlGenerator->generate('products', ['category' => $taxon->getPermalink()]),
                'label' => $taxon->getName()
            ]);
        }
        
        /*$catalog = $menu->addChild('Compositions', [
            'uri' => 'compositions',
            'label' => $this->translator->trans('frontend.menu_items.header.compositions')
        ]);*/

        if ($user->isContentUser()) {
            $menu->addChild('specifications', [
                'uri' => $this->urlGenerator->generate('specifications'),
                'label' => $this->translator->trans('frontend.menu_items.header.specifications')
            ]);

            $menu->addChild('buyers', [
                'uri' => $this->urlGenerator->generate('specification_buyers'),
                'label' => $this->translator->trans('frontend.menu_items.header.specification_buyers')
            ]);
        }

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

        $menu->addChild('user_relations', [
            'uri' => $this->urlGenerator->generate('factory_profile_user_relations'),
            'label' => $this->translator->trans('frontend.user_relations')
        ]);

        $menu->addChild('default_relation', [
            'uri' => $this->urlGenerator->generate('factory_profile_default_relation'),
            'label' => $this->translator->trans('frontend.default_relation')
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

        $menu->addChild('factory_rates', [
            'uri' => $this->urlGenerator->generate('content_user_profile_factory_rates'),
            'label' => $this->translator->trans('frontend.factory_rates')
        ]);

        $menu->addChild('factory_relations', [
            'uri' => $this->urlGenerator->generate('content_user_profile_factory_relations'),
            'label' => $this->translator->trans('frontend.factory_relations')
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
            'uri' => '#',
            'label' => $this->translator->trans('frontend.factory_side.menu.general')
        ]);

        $menu->addChild('news', [
            'uri' => $this->urlGenerator->generate('factory_side_news', ['factory' => $factory->getId()]),
            'label' => $this->translator->trans('frontend.factory_side.menu.news')
        ]);
        
        $menu->addChild('collections', [
            'uri' => '#',
            'label' => $this->translator->trans('frontend.factory_side.menu.collections')
        ]);
        
        $menu->addChild('work_info', [
            'uri' => '#',
            'label' => $this->translator->trans('frontend.factory_side.menu.work_info')
        ]);
        
        $menu->addChild('contacts', [
            'uri' => $this->urlGenerator->generate('factory_side_contacts', ['factory' => $factory->getId()]),
            'label' => $this->translator->trans('frontend.factory_side.menu.contacts')
        ]);
        
        $menu->addChild('circulars', [
            'uri' => $this->urlGenerator->generate('factory_side_circulars', ['factory' => $factory->getId()]),
            'label' => $this->translator->trans('frontend.factory_side.menu.circulars')
        ]);
        
        return $menu;
    }
}
