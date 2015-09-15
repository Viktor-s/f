<?php

namespace Furniture\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FiveLab\Component\Exception\UnexpectedTypeException;

class ProductExtensionVariant
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var ProductExtension
     */
    private $extension;

    /**
     * @var Collection|ProductExtensionOptionValue[]
     */
    private $values;

    /**
     * @var bool
     */
    private $available = true;

    /**
     * @var string
     */
    private $name;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->values = new ArrayCollection();
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
     * Set extension
     *
     * @param ProductExtension $extension
     *
     * @return ProductExtension
     */
    public function setExtension(ProductExtension $extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get product extension
     *
     * @return ProductExtension
     */
    public function getProductExtension()
    {
        return $this->extension;
    }

    /**
     * Set values
     *
     * @param Collection|ProductExtensionOptionValue[] $values
     *
     * @return ProductExtensionVariant
     */
    public function setValues(Collection $values)
    {
        $values->forAll(function ($optionValue) {
            if (!$optionValue instanceof ProductExtensionOptionValue) {
                throw UnexpectedTypeException::create($optionValue, ProductExtensionOptionValue::class);
            }

            return true;
        });

        $this->values = $values;

        return $this;
    }

    /**
     * Get values
     *
     * @return Collection|ProductExtensionOptionValue[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * Set available status
     *
     * @param bool $available
     *
     * @return ProductExtensionVariant
     */
    public function setAvailable($available)
    {
        $this->available = (bool) $available;

        return $this;
    }

    /**
     * Get available
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->available;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ProductExtensionVariant
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
     * Generate name from values
     *
     * @return ProductExtensionVariant
     */
    public function generateNameFromValues()
    {
        $parts = [];

        foreach ($this->values as $value) {
            $parts[] = $value->getValue();
        }

        $name = sprintf(
            '%s: %s',
            $this->extension->getName(),
            implode(', ', $parts)
        );

        $this->name = $name;

        return $this;
    }

    /**
     * Implement __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name ?: '';
    }
}
