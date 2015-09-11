<?php

namespace Furniture\ProductBundle\Entity;

/**
 * Value of option of product extension
 */
class ProductExtensionOptionValue
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var ProductExtension
     */
    protected $extension;

    /**
     * @var ProductExtensionOption
     */
    protected $option;

    /**
     * @var string
     */
    private $value;

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
     * Set extension
     *
     * @param ProductExtension $extension
     *
     * @return ProductExtensionOptionValue
     */
    public function setExtension(ProductExtension $extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension
     *
     * @return ProductExtension
     */
    public function getExtension()
    {
        return $this->extension;
    }

    /**
     * Set option
     *
     * @param ProductExtensionOption $option
     *
     * @return ProductExtensionOptionValue
     */
    public function setOption(ProductExtensionOption $option)
    {
        $this->option = $option;

        return $this;
    }

    /**
     * Get option
     *
     * @return ProductExtensionOption
     */
    public function getOption()
    {
        return $this->option;
    }

    /**
     * Set value
     *
     * @param string $value
     *
     * @return ProductExtensionOptionValue
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
}
