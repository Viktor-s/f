<?php

namespace Furniture\ProductBundle\Entity;

use FiveLab\Component\Exception\UnexpectedTypeException;
use Sylius\Component\Translation\Model\AbstractTranslatable;
use Sylius\Component\Translation\Model\TranslationInterface;
use Sylius\Component\Variation\Model\Option as BaseOption;
use Sylius\Component\Variation\Model\OptionValueInterface;

/**
 * Product material option
 */
class ProductPartMaterialOption
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $name;

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
     * Set name
     *
     * @param string $name
     *
     * @return ProductPartMaterialOption
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
     * Implement __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getName() ?: '';
    }
}
