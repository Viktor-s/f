<?php

namespace Furniture\ProductBundle\Entity;

use Sylius\Component\Translation\Model\AbstractTranslation;
use Symfony\Component\Validator\Constraints as Assert;

class TypeTranslation extends AbstractTranslation
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    private $name;

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
     * @return TypeTranslation
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
}
