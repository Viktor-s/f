<?php

namespace Furniture\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Translation\Model\AbstractTranslatable;
use Symfony\Component\Validator\Constraints as Assert;

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
     *
     * @Assert\NotBlank()
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
     * @var \Doctrine\Common\Collections\Collection|Style[]
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
     * @return string|null
     */
    public function getName(){
        return $this->translate() ? $this->translate()->getName() : ''; 
    }
    
    /**
     * Implement __toString
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->getName();
    }
}
