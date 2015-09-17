<?php

namespace Furniture\CompositionBundle\Entity;

use Sylius\Component\Taxonomy\Model\Taxon;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Item for composition template for relation to category
 */
class CompositeTemplateItem
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var Taxon
     *
     * @Assert\NotBlank
     */
    protected $taxon;

    /**
     * @var CompositeTemplate
     */
    protected $template;

    /**
     * @var int
     *
     * @Assert\NotNull
     */
    protected $position = 0;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Range(min="1")
     */
    protected $count;

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
     * Set composite template
     *
     * @param CompositeTemplate $template
     *
     * @return CompositeTemplateItem
     */
    public function setTemplate($template)
    {
        $this->template = $template;

        return $this;
    }

    /**
     * Get composite template
     *
     * @return CompositeTemplate
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * Set taxon
     *
     * @param Taxon $taxon
     *
     * @return CompositeTemplateItem
     */
    public function setTaxon(Taxon $taxon)
    {
        $this->taxon = $taxon;

        return $this;
    }

    /**
     * Get taxon
     *
     * @return Taxon
     */
    public function getTaxon()
    {
        return $this->taxon;
    }

    /**
     * Set position
     *
     * @param int $position
     *
     * @return CompositeTemplateItem
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
     * Set count items
     *
     * @param int $count
     *
     * @return CompositeTemplateItem
     */
    public function setCount($count)
    {
        $this->count = $count;

        return $this;
    }

    /**
     * Get count items
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }
}
