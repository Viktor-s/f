<?php

namespace Furniture\ProductBundle\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FrontendBundle\Repository\FactoryRepository;
use Furniture\FrontendBundle\Repository\Query\ProductQuery;
use Furniture\ProductBundle\Entity\Product;
use Furniture\FrontendBundle\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;

class ProductController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var CacheManager
     */
    private $imagineCacheManager;

    /**
     * Construct
     *
     * @param EntityManagerInterface        $em
     * @param TokenStorageInterface         $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param CacheManager                  $imagineCacheManager
     */
    public function __construct(
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        CacheManager $imagineCacheManager
    )
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->imagineCacheManager = $imagineCacheManager;
    }

    /**
     * Get latest product by brand. 
     *
     * @param Request $request
     * @param int     $factory
     *
     * @return JsonResponse
     */
    public function getProductDataForBrand(Request $request, $factory)
    {
        $factoryRepository = new FactoryRepository($this->em);
        $factory = $factoryRepository->find($factory);

        if (!$factory) {
            throw new NotFoundHttpException(sprintf(
                'Not found brand with identifier "%s".',
                $factory
            ));
        }

        /** @var ProductRepository $productRepository */
        $productRepository = new ProductRepository($this->em);
        $productQuery = new ProductQuery();

        $productQuery->withFactory($factory);
        $productQuery->setOrderBy('id');
        $productQuery->setOrderDirection('ASC');
        $productQuery->setLimit(1);

        $productQuery->isOnlyAvailable();

        $products = $productRepository->findBy($productQuery, null, null);

        if (empty($products)) {
            throw new NotFoundHttpException(sprintf(
                'Products not found for brand %s.',
                $factory->getName()
            ));
        }

        /** @var Product $product */
        $product = array_shift($products);

        // Check product for retailer
        $this->checkProductForRetailer($product);

        return new JsonResponse([
            'id'       => $product->getId(),
            'image'    => !empty($product->getImage()) ? $this->imagineCacheManager->getBrowserPath($product->getImage()->getPath(), 's300x225') : '/img/300x225.png',
            'price'    => $product->getPrice() / 100,
            'currency' => '€',
        ]);
    }

    /**
     * Check product for retailer
     *
     * @param Product $product
     */
    private function checkProductForRetailer(Product $product)
    {
        /** @var \Furniture\UserBundle\Entity\User $activeUser */
        $activeUser = $this->tokenStorage->getToken()->getUser();

        $retailerUserProfile = $activeUser->getRetailerUserProfile();

        if ($retailerUserProfile) {
            $retailerProfile = $retailerUserProfile->getRetailerProfile();

            if ($retailerProfile && $retailerProfile->isDemo()) {
                $factory = $product->getFactory();

                if (!$retailerProfile->hasDemoFactory($factory)) {
                    throw new NotFoundHttpException(sprintf(
                        'The active retailer "%s" is demo and not have rights for view product "%s" from factory "%s".',
                        $retailerProfile->getName(),
                        $product->getName(),
                        $factory->getName()
                    ));
                }
            } else if ($retailerProfile) {
                $factory = $product->getFactory();

                if ($factory) {
                    if (!$this->authorizationChecker->isGranted('VIEW_PRODUCTS', $factory)) {
                        throw new NotFoundHttpException(sprintf(
                            'The active retailer "%s" not have rights for view product "%s [%d]" from factory "%s [%d]".',
                            $retailerProfile->getName(),
                            $product->getName(),
                            $product->getId(),
                            $factory->getName(),
                            $factory->getId()
                        ));
                    }
                }
            }
        }
    }
}
