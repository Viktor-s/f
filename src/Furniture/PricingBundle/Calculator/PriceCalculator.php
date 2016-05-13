<?php

namespace Furniture\PricingBundle\Calculator;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\UserBundle\Entity\User;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FactoryBundle\Entity\RetailerFactoryRate;
use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Entity\ProductVariant;
use Furniture\SpecificationBundle\Entity\Specification;
use Furniture\SpecificationBundle\Entity\SpecificationItem;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Furniture\ProductBundle\Entity\ProductVariantsPattern;

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
     * @param Product $product
     *
     * @return int
     */
    public function calculateForProduct(Product $product)
    {
        $user = $this->getActiveUser();

        if ($user && $user->isRetailer()) {
            // Calculate for content user
            return $this->calculateForRetailerByFactory($product->getFactory(), $user, $product->getPrice());
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

        if ($user && $user->isRetailer()) {
            // Calculate for content user
            $factory = $product->getFactory();

            return $this->calculateForRetailerByFactory($factory, $user, $productVariant->getPrice());
        }

        return $productVariant->getPrice();
    }

    public function calculateForPattern(ProductVariantsPattern $pattern){
        $user = $this->getActiveUser();
        
        /** @var Product $product */
        $product = $pattern->getProduct();
        
        if ($user && $user->isRetailer()) {
            // Calculate for content user
            $factory = $product->getFactory();

            return $this->calculateForRetailerByFactory($factory, $user, $pattern->getPrice());
        }

        return $pattern->getPrice();
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
        if ($specificationItem->getSkuItem()) {
            $productVariant = $specificationItem->getSkuItem()->getProductVariant();
            $amount = $this->calculateForProductVariant($productVariant);
        } else if ($specificationItem->getCustomItem()) {
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
     * 
     * @param Specification $specification
     * @return int
     */
    public function calculateTotalForSpecification(Specification $specification)
    {
        $price = 0;

        foreach ($specification->getItems() as $item) {
            $price += $this->calculateTotalForSpecificationItem($item);
        }

        return $price;
    }


    /**
     * Calculate extra sales for specification
     *
     * @param Specification $specification
     *
     * @return array
     */
    public function calculateExtraSalesForSpecification(Specification $specification)
    {
        $sales = [];
        $price = $this->calculateTotalForSpecification($specification);

        foreach ($specification->getSales() as $sale) {
            if ($sale->getSale()) {
                $activeSale = ($price * ($sale->getSale() / 100));
                $sales[] = $activeSale;
                $price -= $activeSale;
            }
        }

        return $sales;
    }

    /**
     * Calculate for specification
     *
     * @param Specification $specification
     * @param bool          $useSales
     *
     * @return int
     */
    public function calculateForSpecification(Specification $specification, $useSales = true)
    {
        $price = $this->calculateTotalForSpecification($specification);

        if ($useSales) {
            foreach ($this->calculateExtraSalesForSpecification($specification) as $sale) {
                $price -= $sale;
            }
        }

        return $price;
    }

    /**
     * Get active user
     *
     * @return \Furniture\UserBundle\Entity\User|null
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
     * @param Factory                           $factory
     * @param \Furniture\UserBundle\Entity\User $user
     * @param int                               $amount
     *
     * @return integer
     */
    private function calculateForRetailerByFactory(Factory $factory, User $user, $amount)
    {
        /** @var \Furniture\FactoryBundle\Entity\Repository\RetailerFactoryRateRepository $retailerFactoryRateRepository */
        $retailerFactoryRateRepository = $this->em->getRepository(RetailerFactoryRate::class);
        $retailerFactoryRate = $retailerFactoryRateRepository->findByFactoryAndRetailer($factory, $user->getRetailerUserProfile()->getRetailerProfile());

        if ($retailerFactoryRate) {
            // First step: Add coefficient
            $amount = $amount * $retailerFactoryRate->getCoefficient();

            // Second step: Subtract dumping
            if ($retailerFactoryRate->getDumping()) {
                $amount = $amount - ($amount * ($retailerFactoryRate->getDumping() / 100));
            }
        }

        return $amount;
    }
}
