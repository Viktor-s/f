<?php

namespace Furniture\CompositionBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Template of composite
 */
class CompositeTemplate
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var CompositeCollection
     *
     * @Assert\NotBlank()
     */
    protected $collection;

    /**
     * @var Collection|CompositeTemplateItem[]
     *
     * @Assert\Count(min="1")
     * @Assert\Valid
     */
    protected $items;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
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
     * Set name
     *
     * @param string $name
     *
     * @return CompositeTemplate
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
     * Set items
     *
     * @param Collection $items
     *
     * @return CompositeTemplate
     */
    public function setItems(Collection $items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * Get items
     *
     * @return Collection|CompositeTemplateItem[]
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * Set collection
     *
     * @param CompositeCollection $collection
     *
     * @return CompositeTemplate
     */
    public function setCollection(CompositeCollection $collection)
    {
        $this->collection = $collection;

        return $this;
    }

    /**
     * Get collection
     *
     * @return CompositeCollection
     */
    public function getCollection()
    {
        return $this->collection;
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
