<?php

namespace Furniture\ProductBundle\Entity;

use Sylius\Component\Translation\Model\AbstractTranslatable;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Furniture\ProductBundle\Entity\ProductScheme;

class ProductPart extends AbstractTranslatable
{
    /**
     * @var int
     */
    protected $id;
    
    /**
     * @var ProductPartType
     */
    protected $productPartType;
    
    /**
     * @var Product
     */
    protected $product;

    /**
     * @var Collection|ProductPartMaterial[]
     */
    protected $productPartMaterials;

    /**
     *
     * @var \Furniture\ProductBundle\Entity\ProductScheme
     */
    private $productScheme;
    
    /**
     * Construct
     */
    public function __construct()
    {
        parent::__construct();

        $this->productPartMaterials = new ArrayCollection();
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
     * Set product
     *
     * @param Product $product
     *
     * @return ProductPart
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product
     *
     * @return Product
     */
    public function getProduct()
    {
        return $this->product;
    }


    /**
     * Get label
     *
     * @return string
     */
    public function getLabel()
    {
        return $this->translate()->getLabel();
    }
    
    /**
     * Set label
     *
     * @param string $label
     *
     * @return ProductPart
     */
    public function setLabel($label)
    {
        $this->translate()->setLabel($label);

        return $this;
    }
    
    /**
     * Set product part type
     *
     * @param ProductPartType $productPartType
     *
     * @return ProductPart
     */
    public function setProductPartType(ProductPartType $productPartType)
    {
        $this->productPartType = $productPartType;

        return $this;
    }
    
    /**
     * Get product part type
     *
     * @return ProductPartType
     */
    public function getProductPartType()
    {
        return $this->productPartType;
    }
    
    /**
     * Set product part materials
     *
     * @param Collection|ProductPartMaterial[] $productPartMaterials
     *
     * @return ProductPart
     */
    public function setProductPartMaterials(Collection $productPartMaterials)
    {
        $this->productPartMaterials = $productPartMaterials;

        return $this;
    }
    
    /**
     * Get product part materials
     *
     * @return Collection|ProductPartMaterial[]
     */
    public function getProductPartMaterials()
    {
        return $this->productPartMaterials;
    }

    /**
     * 
     * @return \Furniture\ProductBundle\Entity\ProductScheme
     */
    public function getProductScheme(){
        return $this->productScheme;
    }
    
    /**
     * 
     * @param ProductScheme $productScheme
     * @return \Furniture\ProductBundle\Entity\ProductPart
     */
    public function setProductScheme(ProductScheme $productScheme){
        $this->productScheme = $productScheme;
        return $this;
    }

    
    /**
     * Implement __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->getLabel() ?: '';
    }
}
