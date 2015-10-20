<?php

namespace Furniture\ProductBundle\Entity;

use Furniture\SkuOptionBundle\Entity\SkuOptionType;
use Sylius\Component\Product\Model\Option;

class ProductPdpInput
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var ProductPdpConfig
     */
    private $config;

    /**
     * @var Option
     */
    private $option;

    /**
     * @var SkuOptionType
     */
    private $skuOption;

    /**
     * @var ProductPart
     */
    private $productPart;

    /**
     * @var int
     */
    private $position = 0;

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
     * Set config
     *
     * @param ProductPdpConfig $config
     *
     * @return ProductPdpInput
     */
    public function setConfig(ProductPdpConfig $config)
    {
        $this->config = $config;

        return $this;
    }

    /**
     * Get config
     *
     * @return ProductPdpConfig
     */
    public function getConfig()
    {
        return $this->config;
    }

    /**
     * Set option
     *
     * @param Option $option
     *
     * @return ProductPdpInput
     */
    public function setOption(Option $option)
    {
        $this->clearMappingFields();
        $this->option = $option;

        return $this;
    }

    /**
     * Get option
     *
     * @return Option|null
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * Set product part
     *
     * @param ProductPart $productPart
     *
     * @return ProductPdpInput
     */
    public function setProductPart(ProductPart $productPart)
    {
        $this->productPart = $productPart;

        return $this;
    }

    /**
     * Get product part
     *
     * @return ProductPart|null
     */
    public function getProductPart()
    {
        return $this->productPart;
    }

    /**
     * Set sku option
     *
     * @param SkuOptionType $skuOption
     *
     * @return ProductPdpInput
     */
    public function setSkuOption(SkuOptionType $skuOption)
    {
        $this->clearMappingFields();
        $this->skuOption = $skuOption;

        return $this;
    }

    /**
     * Get sku option
     *
     * @return SkuOptionType|null
     */
    public function getSkuOption()
    {
        return $this->skuOption;
    }

    /**
     * Set position
     *
     * @param int $position
     *
     * @return ProductPdpInput
     */
    public function setPosition($position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * Get position
     *
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * Get human name
     *
     * @return string
     */
    public function getHumanName()
    {
        if ($this->productPart) {
            /** @var ProductPartTranslation $translation */
            $translation = $this->productPart->translate();

            return sprintf(
                'Product part: %s',
                $translation->getLabel()
            );
        } else if ($this->skuOption) {
            return sprintf(
                'SKU Option: %s',
                $this->skuOption->getName()
            );
        } else if ($this->option) {
            return sprintf(
                'Option: %s',
                $this->option->getName()
            );
        } else {
            return 'Undefined';
        }
    }

    /**
     * Clear mapping fields
     */
    private function clearMappingFields()
    {
        $this->option = null;
        $this->skuOption = null;
        $this->productPart = null;
    }
}
