<?php

namespace Furniture\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Translation\Model\AbstractTranslatable;
use Symfony\Component\Validator\Constraints as Assert;

class Category extends AbstractTranslatable
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Category
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
     * @var \Doctrine\Common\Collections\Collection|Category[]
     */
    private $children;

    /**
     * @var \Doctrine\Common\Collections\Collection|CategoryTranslation[]
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
     * @param Category $parent
     *
     * @return Category
     */
    public function setParent(Category $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Category|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Has parent
     *
     * @return bool
     */
    public function hasParent()
    {
        return (bool) $this->parent;
    }

    /**
     * Set slug
     *
     * @param string $slug
     *
     * @return Category
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
     * @return Category
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
        /** @var CategoryTranslation $translation */
        $translation = $this->translate();

        if ($translation) {
            return $translation->getName() ?: '';
        }

        return '';
    }
}
