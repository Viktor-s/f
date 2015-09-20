<?php

namespace Furniture\FactoryBundle\Entity;

use Sylius\Component\Translation\Model\AbstractTranslatable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Furniture\FactoryBundle\Entity\FactoryImage;

class Factory extends AbstractTranslatable
{
    
    /**
     *
     * @var integer
     */
    protected $id;
    
    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $images;
    
    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $products;
    
    /**
     *
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $userRelations;
            
    function __construct() {
        parent::__construct();
        $this->images = new ArrayCollection();
        $this->products = new ArrayCollection();
        $this->userRelations = new ArrayCollection();
    }
    
    /**
     * 
     * @return integer
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
     * @return \Furniture\FactoryBundle\Entity\Factory
     */
    public function setName($name){
        $this->translate()->setName($name);
        return $this;
    }
    
    /**
     * 
     * @return string
     */
    public function getDescription(){
        return $this->translate()->getDescription();
    }
    
    /**
     * 
     * @param string $name
     * @return \Furniture\FactoryBundle\Entity\Factory
     */
    public function setDescription($description){
        $this->translate()->setDescription($description);
        return $this;
    }
    
    /**
     * 
     * @return bool
     */
    public function hasImages(){
        return (bool)!$this->images->isEmpty();
    }
    
    /**
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getImages(){
        return $this->images;
    }
    
    /**
     * 
     * @param Collection $images
     * @return \Furniture\FactoryBundle\Entity\Factory
     */
    public function setImages(Collection $images){
        $this->images = $images;
        return $this;
    }
    
    /**
     * 
     * @param Image $image
     * @return bool
     */
    public function hasImage(FactoryImage $image){
        return $this->images->contains($image);
    }
    
    /**
     * 
     * @param Image $image
     * @return \Furniture\FactoryBundle\Entity\Factory
     */
    public function addImage(FactoryImage $image){
        if(!$this->hasImage($image)){
            $image->setFactory($this);
            $this->images->add($image);
        }
        return $this;
    }
    
    /**
     * 
     * @param Image $image
     * @return \Furniture\FactoryBundle\Entity\Factory
     */
    public function removeImage(FactoryImage $image){
        if($this->hasImage($image)){
            $this->images->removeElement($image);
        }
        return $this;
    }
    
    /**
     * 
     * @return bool
     */
    public function hasProducts(){
        return (bool)!$this->products->isEmpty();
    }
    
    /**
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getProducts(){
        return $this->products;
    }
    
    /**
     * 
     * @param Collection $products
     * @return \Furniture\FactoryBundle\Entity\Factory
     */
    public function setProducts(Collection $products){
        $this->products = $products;
        return $this;
    }
    
    /**
     * 
     * @param \Furniture\ProductBundle\Entity\Product $product
     * @return bool
     */
    public function hasProduct(\Furniture\ProductBundle\Entity\Product $product){
        return $this->products->contains($product);
    }
    
    /**
     * 
     * @param \Furniture\ProductBundle\Entity\Product $product
     * @return \Furniture\FactoryBundle\Entity\Factory
     */
    public function addProduct(\Furniture\ProductBundle\Entity\Product $product){
        if(!$this->hasProduct($product)){
            $this->products->add($product);
        }
        return $this;
    }
    
    /**
     * 
     * @param \Furniture\ProductBundle\Entity\Product $product
     * @return \Furniture\FactoryBundle\Entity\Factory
     */
    public function removeProduct(\Furniture\ProductBundle\Entity\Product $product){
        if($this->hasProduct($product)){
            $this->products->removeElement($product);
        }
        return $this;
    }
    
    /**
     * 
     * @return bool
     */
    public function hasUserRelations(){
        return (bool)!$this->userRelations->isEmpty();
    }
    
    /**
     * 
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getUserRelations(){
        return $this->userRelations;
    }
    
    /**
     * 
     * @param Collection $userRelations
     * @return \Furniture\FactoryBundle\Entity\Factory
     */
    public function setUserRelations(Collection $userRelations){
        $this->userRelations = $userRelations;
        return $this;
    }
    
    /**
     * 
     * @param \Furniture\FactoryBundle\Entity\FactoryUserRelation $userRelation
     * @return bool
     */
    public function hasUserRelation(FactoryUserRelation $userRelation){
        return $this->userRelations->contains($userRelation);
    }
    
    /**
     * 
     * @param \Furniture\FactoryBundle\Entity\FactoryUserRelation $userRelation
     * @return \Furniture\FactoryBundle\Entity\Factory
     */
    public function addUserRelation(FactoryUserRelation $userRelation){
        if(!$this->hasUserRelation($userRelation)){
            $userRelation->setFactory($this);
            $this->userRelations->add($userRelation);
        }
        return $this;
    }
    
    /**
     * 
     * @param \Furniture\FactoryBundle\Entity\FactoryUserRelation $userRelation
     * @return \Furniture\FactoryBundle\Entity\Factory
     */
    public function removeUserRelation(FactoryUserRelation $userRelation){
        if($this->hasUserRelation($userRelation)){
            $this->userRelations->removeElement($userRelation);
        }
        return $this;
    }
    
    
}