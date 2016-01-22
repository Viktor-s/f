<?php

namespace Furniture\ProductBundle\Entity;

use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Entity\ProductVariant;
use Furniture\ProductBundle\Entity\ProductPart;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Translation\Model\AbstractTranslatable;
use Sylius\Component\Translation\Model\TranslationInterface;

class ProductScheme extends AbstractTranslatable{
    
    /**
     *
     * @var int
     */
    private $id;


    /**
     *
     * @var \Furniture\ProductBundle\Entity\Product
     */
    private $product;
    
    /**
     *
     * @var \Doctrine\Common\Collections\Collection|\Furniture\ProductBundle\Entity\ProductVariant[]
     */
    private $productVariants;
    
    /**
     *
     * @var \Doctrine\Common\Collections\Collection|\Furniture\ProductBundle\Entity\ProductPart[]
     */
    private $productParts;
    
    /**
     * 
     * @return int
     */
    public function getId(){
        return $this->id;
    }

    /**
     * 
     * @return string
     */
    public function getName(){
       return $this->translate()->getName();
    }
    
    /**
     * 
     * @param string $name
     * @return \Furniture\ProductBundle\Entity\ProductScheme
     */
    public function setName($name){
        $this->translate()->setName($name);
        return $this;
    }

    /**
     * 
     * @return \Furniture\ProductBundle\Entity\Product
     */
    public function getProduct(){
        return $this->product;
    }

    /**
     * 
     * @param Product $product
     * @return \Furniture\ProductBundle\Entity\ProductScheme
     */
    public function setProduct(Product $product){
        $this->product = $product;
        return $this;
    }

    /**
     * 
     * @return bool
     */
    public function hasProductVariants(){
        return (bool)$this->productVariants->isEmpty();
    }
    
    /**
     * 
     * @param \Furniture\ProductBundle\Entity\ProductVariant $productVariant
     * @return type
     */
    public function hasProductVariant(ProductVariant $productVariant){
        return $this->productVariants->contains($productVariant);
    }
    
    /**
     * 
     * @return \Doctrine\Common\Collections\Collection|\Furniture\ProductBundle\Entity\ProductVariant[]
     */
    public function getProductVariants(){
        return $this->productVariants;
    }
    
    /**
     * 
     * @param Collection $productVariants
     * @return \Furniture\ProductBundle\Entity\ProductScheme
     */
    public function setProductVariants(Collection $productVariants){
        $this->productVariants = $productVariants;
        return $this;
    }
    
    /**
     * 
     * @param  \Furniture\ProductBundle\Entity\ProductVariant $productVariant
     * @return \Furniture\ProductBundle\Entity\ProductScheme
     */
    public function addProductVariant(ProductVariant $productVariant){
        if(!$this->hasProductVariant($productVariant)){
            $this->productVariants->add($productVariant);
        }
        return $this;
    }
    
    /**
     * 
     * @param ProductVariant $productVariant
     * @return \Furniture\ProductBundle\Entity\ProductScheme
     */
    public function removeProductVariant(ProductVariant $productVariant){
        if($this->hasProductVariant($productVariant)){
            $this->productVariants->removeElement($productVariant);
        }
        return $this;
    }
    
    /**
     * 
     * @return bool
     */
    public function hasProductParts(){
        return (bool)$this->productParts->isEmpty();
    }
    
    /**
     * 
     * @param ProductPart $productPart
     * @return bool
     */
    public function hasProductPart(ProductPart $productPart){
        return $this->productParts->contains($productPart);
    }
    
    /**
     * 
     * @return \Doctrine\Common\Collections\Collection|\Furniture\ProductBundle\Entity\ProductPart[]
     */
    public function getProductParts(){
        return $this->productParts;
    }
    
    /**
     * 
     * @param Collection $productParts
     * @return \Furniture\ProductBundle\Entity\ProductScheme
     */
    public function setProductParts(Collection $productParts){
        $this->productParts = $productParts;
        return $this;
    }
    
    /**
     * 
     * @param \Furniture\ProductBundle\Entity\ProductPart $productPart
     * @return \Furniture\ProductBundle\Entity\ProductScheme
     */
    public function addProductPart(ProductPart $productPart){
        if(!$this->hasProductPart($productPart)){
            $this->productParts->add($productPart);
        }
        return $this;
    }
    
    /**
     * 
     * @param ProductPart $productPart
     * @return \Furniture\ProductBundle\Entity\ProductScheme
     */
    public function removeProductPart(ProductPart $productPart){
        if($this->hasProductPart($productPart)){
            $this->productParts->removeElement($productPart);
        }
        return $this;
    }
    
}

