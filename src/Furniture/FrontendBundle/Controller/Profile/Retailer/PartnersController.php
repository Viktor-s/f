<?php

namespace Furniture\FrontendBundle\Controller\Profile\Retailer;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FrontendBundle\Repository\FactoryRepository;
use Furniture\FrontendBundle\Repository\FactoryRetailerRelationRepository;
use Furniture\FrontendBundle\Repository\PostRepository;
use Furniture\FrontendBundle\Repository\ProductCategoryRepository;
use Furniture\FrontendBundle\Repository\ProductStyleRepository;
use Furniture\FrontendBundle\Repository\CompositeCollectionRepository;
use Furniture\FrontendBundle\Repository\Query\CompositeCollectionQuery;
use Furniture\FrontendBundle\Repository\Query\FactoryQuery;
use Furniture\ProductBundle\Entity\Category;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PartnersController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FactoryRepository
     */
    private $factoryRepository;

    /**
     * @var FactoryRetailerRelationRepository
     */
    private $factoryRetailerRelationRepository;

    /**
     * @var PostRepository
     */
    private $postRepository;

    /**
     * @var ProductStyleRepository
     */
    private $productStyleRepository;

    /**
     * @var ProductCategoryRepository
     */
    private $productCategoryRepository;

    /**
     * @var CompositeCollectionRepository
     */
    private $compositeCollectionRepository;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * Construct
     *
     * @param \Twig_Environment                 $twig
     * @param FactoryRepository                 $factoryRepository
     * @param FactoryRetailerRelationRepository $factoryRetailerRelationRepository
     * @param PostRepository                    $postRepository
     * @param ProductStyleRepository            $productStyleRepository
     * @param ProductCategoryRepository         $productCategoryRepository
     * @param CompositeCollectionRepository     $compositeCollectionRepository
     * @param TokenStorageInterface             $tokenStorage
     * @param AuthorizationCheckerInterface     $authorizationChecker
     */
    public function __construct(
        \Twig_Environment $twig,
        FactoryRepository $factoryRepository,
        FactoryRetailerRelationRepository $factoryRetailerRelationRepository,
        PostRepository $postRepository,
        ProductStyleRepository $productStyleRepository,
        ProductCategoryRepository $productCategoryRepository,
        CompositeCollectionRepository $compositeCollectionRepository,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker
    )
    {
        $this->twig = $twig;
        $this->factoryRepository = $factoryRepository;
        $this->factoryRetailerRelationRepository = $factoryRetailerRelationRepository;
        $this->postRepository = $postRepository;
        $this->productStyleRepository = $productStyleRepository;
        $this->productCategoryRepository = $productCategoryRepository;
        $this->compositeCollectionRepository = $compositeCollectionRepository;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * View factories
     *
     * @param Request $request
     *
     * @return Response
     */
    public function partners(Request $request)
    {
        $styles = $this->productStyleRepository->findAllOnlyRoot();
        $categories = $this->productCategoryRepository->findAllOnlyRoot();

        // Resolve selected style
        $selectedStyle = null;

        if ($request->query->has('style')) {
            foreach ($styles as $style) {
                if ($style->getId() == $request->query->get('style')) {
                    $selectedStyle = $style;
                    break;
                }
            }
        }

        // Resolve selected category
        $selectedCategory = null;

        if ($request->query->has('category')) {
            foreach ($categories as $category) {
                if ($category->getId() == $request->query->get('category')) {
                    $selectedCategory = $category;
                    break;
                }
            }
        }

        // Create and populate query for search factories
        $query = new FactoryQuery();

        if ($selectedStyle) {
            $query->withStyle($selectedStyle);
        }

        if ($selectedCategory) {
            $query->withCategory($selectedCategory);
        }

        /** @var \Furniture\UserBundle\Entity\User $activeUser */
        $activeUser = $this->tokenStorage->getToken()->getUser();
        $query->withRetailerFromUser($activeUser);

        $retailerProfile = null;

        if ($activeUser->getRetailerUserProfile()) {
            $retailerProfile = $activeUser->getRetailerUserProfile()
                ->getRetailerProfile();
        }

        if ($retailerProfile) {
            $query
                ->withRetailer($retailerProfile)
                ->withoutRetailerAccessControl();

            if ($retailerProfile->isDemo()) {
                $query
                    ->withoutOnlyEnabledOrDisabled()
                    ->withoutRetailerAccessControl();
            }
        }

        $factories = $this->factoryRepository->findBy($query);

        $content = $this->twig->render('FrontendBundle:Profile/Retailer/Partners:factories.html.twig', [
            'factories'         => $factories,
            'styles'            => $styles,
            'selected_style'    => $selectedStyle,
            'categories'        => $categories,
            'selected_category' => $selectedCategory,
        ]);

        return new Response($content);
    }

    /**
     * General info about factory
     *
     * @param $factory
     *
     * @return Response
     */
    public function partnerGeneral($factory)
    {
        $factory = $this->findFactory($factory);
        $this->checkFactoryForRetailer($factory);

        $content = $this->twig->render('FrontendBundle:Profile/Retailer/Partners:general.html.twig', [
            'factory'                   => $factory,
            'factory_retailer_relation' => $this->findFactoryRetailerRelation($factory),
        ]);

        return new Response($content);
    }

    /**
     * View factory news
     *
     * @param int $factory
     *
     * @return Response
     */
    public function news($factory)
    {
        $factory = $this->findFactory($factory);
        $this->checkFactoryForRetailer($factory);

        $posts = $this->postRepository->findNewsForFactory($factory);

        $content = $this->twig->render('FrontendBundle:Profile/Retailer/Partners:posts.html.twig', [
            'posts'                     => $posts,
            'factory'                   => $factory,
            'circulars'                 => false,
            'factory_retailer_relation' => $this->findFactoryRetailerRelation($factory),
        ]);

        return new Response($content);
    }

    /**
     * View factory circulars
     *
     * @param int $factory
     *
     * @return Response
     */
    public function circulars($factory)
    {
        $factory = $this->findFactory($factory);
        $this->checkFactoryForRetailer($factory);

        // Check active state for factory retailer relation.
        if (!$this->authorizationChecker->isGranted('ACTIVE_RELATION', $factory)) {
            throw new AccessDeniedException(sprintf(
                'The user "%s" not have rights for view factory %s.',
                $this->tokenStorage->getToken()->getUsername(),
                $factory->getName()
            ));
        }

        $posts = $this->postRepository->findCircularsForFactory($factory);

        $content = $this->twig->render('FrontendBundle:Profile/Retailer/Partners:posts.html.twig', [
            'posts'                     => $posts,
            'factory'                   => $factory,
            'circulars'                 => true,
            'factory_retailer_relation' => $this->findFactoryRetailerRelation($factory),
        ]);

        return new Response($content);
    }

    /**
     * View post element
     *
     * @param int    $factory
     * @param string $slug
     *
     * @return Response
     */
    public function post($factory, $slug)
    {
        $factory = $this->findFactory($factory);
        $this->checkFactoryForRetailer($factory);
        $post = $this->postRepository->findBySlugForFactory($factory, $slug);

        if (!$post) {
            throw new NotFoundHttpException(sprintf(
                'Not found post with slug "%s" for factory "%s [%d]".',
                $slug,
                $factory->getName(),
                $factory->getId()
            ));
        }

        $content = $this->twig->render('FrontendBundle:Profile/Retailer/Partners:post.html.twig', [
            'post'                      => $post,
            'factory'                   => $factory,
            'factory_retailer_relation' => $this->findFactoryRetailerRelation($factory),
        ]);

        return new Response($content);
    }

    /**
     * View factory contacts
     *
     * @param int $factory
     *
     * @return Response
     */
    public function contacts($factory)
    {
        $factory = $this->findFactory($factory);
        $this->checkFactoryForRetailer($factory);

        $content = $this->twig->render('FrontendBundle:Profile/Retailer/Partners:contacts.html.twig', [
            'factory'                   => $factory,
            'contacts'                  => $factory->getContacts(),
            'factory_retailer_relation' => $this->findFactoryRetailerRelation($factory),
        ]);

        return new Response($content);
    }

    /**
     * Find factory by identifier
     *
     * @param int $factory
     *
     * @return Factory
     *
     * @throws NotFoundHttpException
     */
    private function findFactory($factory)
    {
        $factory = $this->factoryRepository->find($factoryId = $factory);

        if (!$factory) {
            throw new NotFoundHttpException(sprintf(
                'Not found factory with identifier "%s".',
                $factoryId
            ));
        }

        return $factory;
    }

    /**
     * Check factory for retailer
     *
     * @param Factory $factory
     */
    private function checkFactoryForRetailer(Factory $factory)
    {
        /** @var \Furniture\UserBundle\Entity\User $activeUser */
        $activeUser = $this->tokenStorage->getToken()->getUser();
        $retailerUserProfile = $activeUser->getRetailerUserProfile();

        if ($retailerUserProfile) {
            $retailerProfile = $retailerUserProfile->getRetailerProfile();

            if ($retailerProfile && $retailerProfile->isDemo()) {
                if (!$retailerProfile->hasDemoFactory($factory)) {
                    throw new NotFoundHttpException(sprintf(
                        'The retailer "%s" is demo and not have rights for view factory "%s".',
                        $retailerProfile->getName(),
                        $factory->getName()
                    ));
                }

                return;
            }
        }

        // By task: #265
        //if ($factory->isDisabled()) {
        //    throw new NotFoundHttpException(sprintf(
        //        'The factory "%s" is disabled.',
        //        $factory->getName()
        //    ));
        //}
    }

    /**
     * Find factory retailer relation
     *
     * @param Factory $factory
     *
     * @return \Furniture\FactoryBundle\Entity\FactoryRetailerRelation
     */
    private function findFactoryRetailerRelation(Factory $factory)
    {
        /** @var \Furniture\UserBundle\Entity\User $activeUser */
        $activeUser = $this->tokenStorage->getToken()->getUser();

        if ($activeUser->isNoRetailer()) {
            return null;
        }

        $retailer = $activeUser->getRetailerUserProfile()->getRetailerProfile();

        return $this->factoryRetailerRelationRepository->findRelationBetweenFactoryAndRetailer($factory, $retailer);
    }
}
