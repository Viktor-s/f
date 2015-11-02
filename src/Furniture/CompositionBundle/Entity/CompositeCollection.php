<?php

namespace Furniture\CompositionBundle\Entity;

use Sylius\Component\Translation\Model\AbstractTranslatable;
use Doctrine\ORM\Mapping as ORM;

class CompositeCollection extends AbstractTranslatable
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
     * @param str $presentation
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
     * @return str
     */
    public function getPresentation()
    {
        return $this->translate()->getPresentation($presentation);
    }
    
    /**
     * Set translatable description
     * 
     * @param str $description
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
     * @return str
     */
    public function getDescription()
    {
        return $this->translate()->getDescription();
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
