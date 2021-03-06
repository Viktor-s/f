<?php

namespace Furniture\CompositionBundle\Entity;

use Sylius\Component\Translation\Model\AbstractTranslation;

class CompositeTranslation extends AbstractTranslation
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $presentation;

    /**
     * @var string
     */
    protected $description;

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
     * Set presentation
     *
     * @param string $presentation
     *
     * @return CompositeTranslation
     */
    public function setPresentation($presentation)
    {
        $this->presentation = $presentation;

        return $this;
    }

    /**
     * Get presentation
     *
     * @return string
     */
    public function getPresentation()
    {
        return $this->presentation;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return CompositeTranslation
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }
}
