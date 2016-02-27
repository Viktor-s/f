<?php

namespace Furniture\ProductBundle\Entity;

use Sylius\Component\Translation\Model\AbstractTranslation;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Translation for product extension
 */
class ProductPartMaterialTranslation extends AbstractTranslation
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @Assert\NotBlank()
     *
     * @var string
     */
    protected $presentation;

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
     * @return ProductPartMaterialTranslation
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
}
