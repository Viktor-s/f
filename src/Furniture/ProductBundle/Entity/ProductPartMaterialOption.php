<?php

namespace Furniture\ProductBundle\Entity;

use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotBlank()
     *
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
