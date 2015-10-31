<?php

namespace Furniture\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Translation\Model\AbstractTranslatable;

class Style extends AbstractTranslatable
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Style
     */
    private $parent;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var int
     */
    private $position;

    /**
     * @var \Doctrine\Common\Collections\Collection|Style[]
     */
    private $children;

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
     * Set parent style
     *
     * @param Style $style
     *
     * @return Style
     */
    public function setParent(Style $style = null)
    {
        $this->parent = $style;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Style
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
     * @return Style
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
     * @return Style
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
     * Implement __toString
     *
     * @return string
     */
    public function __toString()
    {
        /** @var StyleTranslation $translate */
        $translate = $this->translate();

        if ($translate) {
            return $translate->getName() ?: '';
        }

        return '';
    }
}
