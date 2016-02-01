<?php

namespace Furniture\ProductBundle\Entity;

use Sylius\Component\Translation\Model\AbstractTranslation;
use Symfony\Component\Validator\Constraints as Assert;

class ProductPartTranslation extends AbstractTranslation
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
    protected $label;

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
     * Set label
     *
     * @param string $label
     *
     * @return ProductPartTranslation
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
}
