<?php

namespace Furniture\SpecificationBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Furniture\CommonBundle\Entity\User;
use Furniture\SpecificationBundle\Model\GroupedCustomItemsByFactory;
use Furniture\SpecificationBundle\Model\GroupedItemsByFactory;
use Symfony\Component\Validator\Constraints as Assert;

class Specification
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $name;

    /**
     * @var string
     */
    private $description;

    /**
     * @var Collection|SpecificationItem[]
     */
    private $items;

    /**
     * @var Buyer
     */
    private $buyer;

    /**
     * @var \DateTime
     */
    private $createdAt;

    /**
     * @var \DateTime
     */
    private $updatedAt;

    /**
     * @var \DateTime
     */
    private $finishedAt;

    /**
     * @var Collection|SpecificationSale[]
     */
    private $sales;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->sales = new ArrayCollection();
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set user
     *
     * @param User $user
     *
     * @return Specification
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return User
     *
     * @deprecated Please use getOwner method
     */
    public function getUser()
    {
        trigger_error('The method "getUser" is deprecated and will be removed. Please use "getCreator" method.');

        return $this->user;
    }

    /**
     * Get creator
     *
     * @return User
     */
    public function getCreator()
    {
        return $this->user;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Specification
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Specification
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set items
     *
     * @param Collection $items
     *
     * @return Specification
     */
    public function setItems(Collection $items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Get items
     *
     * @return Collection|SpecificationItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Get grouped items by factory
     *
     * @return Collection|GroupedItemsByFactory[]
     */
    public function getGroupedVariantItemsByFactory()
    {
        $grouped = [];

        foreach ($this->items as $item) {
            if ($skuVariant = $item->getSkuItem()){
                $variant = $skuVariant->getProductVariant();
                /** @var \Furniture\ProductBundle\Entity\Product $product */
                $product = $variant->getProduct();
                $factory = $product->getFactory();

                if (!isset($grouped[$factory->getId()])) {
                    $grouped[$factory->getId()] = [
                        'factory' => $factory,
                        'items' => new ArrayCollection()
                    ];
                }

                /** @var Collection $items */
                $items = $grouped[$factory->getId()]['items'];
                $items->add($item);
            }
        }

        $result = new ArrayCollection();

        foreach ($grouped as $groupInfo) {
            $result->add(new GroupedItemsByFactory($groupInfo['factory'], $groupInfo['items']));
        }

        return $result;
    }

    /**
     * Get grouped custom items by factory
     *
     * @return Collection|GroupedCustomItemsByFactory[]
     */
    public function getGroupedCustomItemsByFactory()
    {
        $grouped = [];

        foreach ($this->items as $item) {
            if ($customItem = $item->getCustomItem()) {
                $factoryName = $customItem->getFactoryName();

                if (!$factoryName) {
                    $factoryName = 'Without name';
                }

                $key = strtolower($factoryName);

                if (!isset($grouped[$key])) {
                    $grouped[$key] = [
                        'factory_name' => $factoryName,
                        'items' => new ArrayCollection()
                    ];
                }

                /** @var Collection $items */
                $items = $grouped[$key]['items'];
                $items->add($item);
            }
        }

        $result = new ArrayCollection();

        foreach ($grouped as $groupInfo) {
            $result->add(new GroupedCustomItemsByFactory($groupInfo['factory_name'], $groupInfo['items']));
        }

        return $result;
    }

    /**
     * Set buyer
     *
     * @param Buyer $buyer
     *
     * @return Specification
     */
    public function setBuyer(Buyer $buyer = null)
    {
        $this->buyer = $buyer;

        return $this;
    }

    /**
     * Get buyer
     *
     * @return Buyer
     */
    public function getBuyer()
    {
        return $this->buyer;
    }

    /**
     * Get created at
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Get updated at
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Finish specification
     */
    public function finish()
    {
        $this->finishedAt = new \DateTime();
    }

    /**
     * Is finished
     *
     * @return bool
     */
    public function isFinished()
    {
        return $this->finishedAt ? true : false;
    }

    /**
     * Get finished at
     *
     * @return \DateTime
     */
    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    /**
     * Set sales
     *
     * @param Collection|SpecificationSale[] $sales
     *
     * @return Specification
     */
    public function setSales(Collection $sales)
    {
        $this->sales = $sales;

        return $this;
    }

    /**
     * Get sales
     *
     * @return Collection|SpecificationSale[]
     */
    public function getSales()
    {
        return $this->sales;
    }

    /**
     * Calculate total price (In EUR)
     *
     * @return int
     */
    public function getTotalPrice()
    {
        $price = 0;

        foreach ($this->items as $item) {
            $price += $item->getTotalPrice();
        }

        return $price;
    }

    /**
     * Get count items
     *
     * @return int
     */
    public function getCountItems()
    {
        $items = 0;

        foreach ($this->items as $item) {
            $items += $item->getQuantity();
        }

        return $items;
    }

    /**
     * Life hook on update
     */
    public function onUpdate()
    {
        $this->updatedAt = new \DateTime();
    }
}
