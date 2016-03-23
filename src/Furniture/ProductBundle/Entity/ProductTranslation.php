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

    /**
     * @var string
     */
    private $searchTsv = '';

    /**
     * @return string
     */
    public function getSearchTsv()
    {
        return $this->searchTsv;
    }

    /**
     * @param string $searchTsv
     * @return ProductTranslation
     */
    public function setSearchTsv($searchTsv)
    {
        $this->searchTsv = $searchTsv;

        return $this;
    }

}
