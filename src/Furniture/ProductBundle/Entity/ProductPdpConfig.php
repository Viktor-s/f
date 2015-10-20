<?php

namespace Furniture\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Furniture\SkuOptionBundle\Entity\SkuOptionType;
use Sylius\Component\Product\Model\Option;

class ProductPdpConfig
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var Collection|ProductPdpInput[]
     */
    private $inputs;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->inputs = new ArrayCollection();
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
     * Set product
     *
     * @param Product $product
     *
     * @return ProductPdpConfig
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Set inputs
     *
     * @param Collection $inputs
     *
     * @return ProductPdpConfig
     */
    public function setInputs(Collection $inputs)
    {
        $this->inputs = $inputs;

        return $this;
    }

    /**
     * Get inputs
     *
     * @return Collection|ProductPdpInput[] $inputs
     */
    public function getInputs()
    {
        return $this->inputs;
    }

    /**
     * Add input
     *
     * @param ProductPdpInput $input
     *
     * @return ProductPdpConfig
     */
    public function addInput(ProductPdpInput $input)
    {
        if (!$this->inputs->contains($input)) {
            $input->setConfig($this);
            $this->inputs->add($input);
        }

        return $this;
    }

    /**
     * Find input for option
     *
     * @param Option $option
     *
     * @return ProductPdpInput|null
     */
    public function findInputForOption(Option $option)
    {
        foreach ($this->inputs as $input) {
            if ($input->getOption() && $input->getOption()->getId() == $option->getId()) {
                return $option;
            }
        }

        return null;
    }

    /**
     * Find input for sku option
     *
     * @param SkuOptionType $skuOption
     *
     * @return ProductPdpInput|null
     */
    public function findInputForSkuOption(SkuOptionType $skuOption)
    {
        foreach ($this->inputs as $input) {
            if ($input->getSkuOption() && $input->getSkuOption()->getId() == $skuOption->getId()) {
                return $skuOption;
            }
        }

        return null;
    }

    /**
     * Find input for product part
     *
     * @param ProductPart $productPart
     *
     * @return ProductPdpInput|null
     */
    public function findInputForProductPart(ProductPart $productPart)
    {
        foreach ($this->inputs as $input) {
            if ($input->getProductPart() && $input->getProductPart()->getId() == $productPart->getId()) {
                return $productPart;
            }
        }

        return null;
    }
}
