<?php

namespace Furniture\ProductBundle\Entity;

use Sylius\Component\Core\Model\ProductTranslation as BaseProductTranslation;
use Symfony\Component\Validator\Constraints as Assert;

class ProductTranslation extends BaseProductTranslation
{
    /**
     * @var string
     *
     * @Assert\NotBlank()
     */
    protected $name;

    /**
     * @var string
     */
    protected $slug;

    /**
     * @var string
     */
    protected $description;
}
