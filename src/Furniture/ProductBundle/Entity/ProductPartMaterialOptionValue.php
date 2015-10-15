<?php

namespace Furniture\ProductBundle\Entity;

/**
 * Value of option of product material
 */
class ProductPartMaterialOptionValue
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var ProductPartMaterial
     */
    protected $material;

    /**
     * @var ProductPartMaterialOption
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
     * Set material
     *
     * @param ProductPartMaterial $material
     *
     * @return ProductPartMaterialOptionValue
     */
    public function setMaterial(ProductPartMaterial $material)
    {
        $this->material = $material;

        return $this;
    }

    /**
     * Get material
     *
     * @return ProductPartMaterial
     */
    public function getMaterial()
    {
        return $this->material;
    }

    /**
     * Set option
     *
     * @param ProductPartMaterialOption $option
     *
     * @return ProductPartMaterialOptionValue
     */
    public function setOption(ProductPartMaterialOption $option)
    {
        $this->option = $option;

        return $this;
    }

    /**
     * Get option
     *
     * @return ProductPartMaterialOption
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
     * @return ProductPartMaterialOptionValue
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

    /**
     * Implement __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->value ?: '';
    }
}
