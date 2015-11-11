<?php

namespace Furniture\CompositionBundle\Entity;

use Sylius\Component\Translation\Model\AbstractTranslatable;
use Doctrine\ORM\Mapping as ORM;
use Furniture\FactoryBundle\Entity\Factory;

class CompositeCollection extends AbstractTranslatable
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var Factory
     */
    private $factory;

    /**
     * @var string
     */
    protected $name;

    /**
     *
     * @var int
     */
    protected $position;


    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();
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
     * Set factory
     *
     * @param Factory $factory
     *
     * @return CompositeCollection
     */
    public function setFactory(Factory $factory = null)
    {
        $this->factory = $factory;

        return $this;
    }

    /**
     * Get factory
     *
     * @return Factory
     */
    public function getFactory()
    {
        return $this->factory;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return CompositeCollection
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
     * Set translatable presentation
     * 
     * @param string $presentation
     *
     * @return CompositeCollection
     */
    public function setPresentation($presentation)
    {
        $this->translate()->setPresentation($presentation);

        return $this;
    }

    /**
     * Get translatable presentation
     * 
     * @return string
     */
    public function getPresentation()
    {
        return $this->translate()->getPresentation();
    }
    
    /**
     * Set translatable description
     * 
     * @param string $description
     *
     * @return CompositeCollection
     */
    public function setDescription($description)
    {
        $this->translate()->setDescription($description);

        return $this;
    }

    /**
     * Get translatable description
     * 
     * @return string
     */
    public function getDescription()
    {
        return $this->translate()->getDescription();
    }

    /**
     * 
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * 
     * @param int $position
     * @return \Furniture\CompositionBundle\Entity\CompositeCollection
     */
    public function setPosition($position)
    {
        $this->position = $position;
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
