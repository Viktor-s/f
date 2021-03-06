<?php

namespace Furniture\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Translation\Model\AbstractTranslatable;
use Symfony\Component\Validator\Constraints as Assert;

class Space extends AbstractTranslatable
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Space
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
     * @var \Doctrine\Common\Collections\Collection|Space[]
     */
    private $children;

    /**
     * @var \Doctrine\Common\Collections\Collection|SpaceTranslation[]
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
     * Set parent space
     *
     * @param Space $space
     *
     * @return Space
     */
    public function setParent(Space $space = null)
    {
        $this->parent = $space;

        return $this;
    }

    /**
     * Get parent
     *
     * @return Space
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
     * @return Space
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
     * @return Space
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
