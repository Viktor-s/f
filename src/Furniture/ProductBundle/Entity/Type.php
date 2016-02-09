<?php

namespace Furniture\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Translation\Model\AbstractTranslatable;
use Symfony\Component\Validator\Constraints as Assert;

class Type extends AbstractTranslatable
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Type
     */
    private $parent;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $slug;

    /**
     * @var int
     */
    private $position;

    /**
     * @var \Doctrine\Common\Collections\Collection|Type[]
     */
    private $children;

    /**
     * @var \Doctrine\Common\Collections\Collection|TypeTranslation[]
     *
     * @Assert\Valid()
     */
    protected $translations;

    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->children = new ArrayCollection();
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
     * Set parent
     *
     * @param Type $parent
     *
     * @return Type
     */
    public function setParent(Type $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Type
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Type
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set position
     *
     * @param int $position
     *
     * @return Type
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
     * Get level
     *
     * @return int
     */
    public function getLevel()
    {
        $level = 0;

        $parent = $this;

        while ($parent = $parent->getParent()) {
            $level++;
        }

        return $level;
    }

    /**
     * Implement __toString
     *
     * @return string
     */
    public function __toString()
    {
        /** @var TypeTranslation $translate */
        $translate = $this->translate();

        if ($translate) {
            return $translate->getName() ?: '';
        }

        return '';
    }
}
