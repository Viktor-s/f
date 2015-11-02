<?php

namespace Furniture\PricingBundle\Calculator;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\Entity\User;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FactoryBundle\Entity\UserFactoryRate;
use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Entity\ProductVariant;
use Furniture\SpecificationBundle\Entity\Specification;
use Furniture\SpecificationBundle\Entity\SpecificationItem;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class PriceCalculator
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Construct
     *
     * @param TokenStorageInterface  $tokenStorage
     * @param EntityManagerInterface $em
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $em
    )
    {
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
    }

    /**
     * Calculate for product in catalog
     *
     * @param Product       $product
     *
     * @return int
     */
    public function calculateForProduct(Product $product)
    {
        $user = $this->getActiveUser();

        if ($user && $user->hasRole(User::ROLE_CONTENT_USER)) {
            // Calculate for content user
            return $this->calculateForContentUserByFactory($product->getFactory(), $user, $product->getPrice());
        }

        return $product->getPrice();
    }

    /**
     * Calculate for product variant
     *
     * @param ProductVariant $productVariant
     *
     * @return int
     */
    public function calculateForProductVariant(ProductVariant $productVariant)
    {
        $user = $this->getActiveUser();

        /** @var Product $product */
        $product = $productVariant->getProduct();

        if ($user && $user->hasRole(User::ROLE_CONTENT_USER)) {
            // Calculate for content user
            $factory = $product->getFactory();

            return $this->calculateForContentUserByFactory($factory, $user, $productVariant->getPrice());
        }

        return $productVariant->getPrice();
    }

    /**
     * Calculate total price for specification item
     *
     * @param SpecificationItem $specificationItem
     *
     * @return int
     */
    public function calculateTotalForSpecificationItem(SpecificationItem $specificationItem)
    {
        /* Calculate amount for specific item type */
        if ($specificationItem->getSkuItem()){
            $productVariant = $specificationItem->getSkuItem()->getProductVariant();
            $amount = $this->calculateForProductVariant($productVariant);
        } else if($specificationItem->getCustomItem()){
            $amount = $specificationItem->getCustomItem()->getPrice();
        } else {
            throw new \RuntimeException('Can not resolve amount. Unavailable mode.');
        }

        $buyer = $specificationItem->getSpecification()->getBuyer();

        if ($buyer && $buyer->hasSale()) {
            $amount = $amount - ($amount * ($buyer->getSale() / 100));
        }

        $amount = $amount * $specificationItem->getQuantity();

        return $amount;
    }

    /**
     * Calculate for specification
     *
     * @param Specification $specification
     *
     * @return int
     */
    public function calculateForSpecification(Specification $specification)
    {
        $price = 0;

        foreach ($specification->getItems() as $item) {
            $price += $this->calculateTotalForSpecificationItem($item);
        }

        foreach ($specification->getSales() as $sale) {
            if ($sale->getSale()) {
                $price -= ($price * ($sale->getSale() / 100));
            }
        }

        return $price;
    }

    /**
     * Get active user
     *
     * @return User|null
     */
    private function getActiveUser()
    {
        $user = null;

        $token = $this->tokenStorage->getToken();

        if ($token) {
            $userInToken = $token->getUser();

            if ($userInToken instanceof User) {
                $user = $userInToken;
            }
        }

        return $user;
    }


    /**
     * Calculate price for product for content user
     *
     * @param Factory $factory
     * @param User    $user
     * @param int     $amount
     *
     * @return integer
     */
    private function calculateForContentUserByFactory(Factory $factory, User $user, $amount)
    {
        /** @var \Furniture\FactoryBundle\Entity\Repository\UserFactoryRateRepository $userFactoryRateRepository */
        $userFactoryRateRepository = $this->em->getRepository(UserFactoryRate::class);
        $userFactoryRate = $userFactoryRateRepository->findByFactoryAndUser($factory, $user);

        if ($userFactoryRate) {
            // First step: Add coefficient
            $amount = $amount * $userFactoryRate->getCoefficient();

            // Second stem: Subtract dumping
            if ($userFactoryRate->getDumping()) {
                $amount = $amount - ($amount * ($userFactoryRate->getDumping() / 100));
            }
        }

        return $amount;
    }
}
