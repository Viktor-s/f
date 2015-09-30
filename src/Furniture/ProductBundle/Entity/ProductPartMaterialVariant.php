<?php

namespace Furniture\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FiveLab\Component\Exception\UnexpectedTypeException;

class ProductPartMaterialVariant
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var ProductPartMaterial
     */
    private $material;

    /**
     * @var Collection|ProductPartMaterialOptionValue[]
     */
    private $values;

    /**
     * @var bool
     */
    private $available = true;

    /**
     * @var string
     */
    private $name;

    /**
     *
     * @var string
     */
    private $descriptionalName;

    /**
     *
     * @var string
     */
    private $descriptionalCode;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->values = new ArrayCollection();
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
     * Set material
     *
     * @param ProductPartMaterial $material
     *
     * @return ProductPartMaterial
     */
    public function setExtension(ProductPartMaterial $material)
    {
        $this->material = $material;

        return $this;
    }
    
    /**
     * 
     * @return ProductPartMaterial
     */
    public function getExtension(){
        return $this->material;
    }

    /**
     * Get product material
     *
     * @return ProductPartMaterial
     */
    public function getProductPartMaterial()
    {
        return $this->material;
    }

    /**
     * Set values
     *
     * @param Collection|ProductPartMaterialOptionValue[] $values
     *
     * @return ProductPartMaterialVariant
     */
    public function setValues(Collection $values)
    {
        $values->forAll(function ($optionValue) {
            if (!$optionValue instanceof ProductPartMaterialOptionValue) {
                throw UnexpectedTypeException::create($optionValue, ProductPartMaterialOptionValue::class);
            }

            return true;
        });

        $this->values = $values;

        return $this;
    }

    /**
     * Get values
     *
     * @return Collection|ProductPartMaterialOptionValue[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * 
     * @return bool
     */
    public function hasValues(){
        return (bool)!$this->values->isEmpty();
    }

    /**
     * 
     * @param \Furniture\ProductBundle\Entity\ProductPartMaterialOptionValue $value
     * @return bool
     */
    public function hasValue(ProductPartMaterialOptionValue $value){
        return $this->values->contains($value);
    }
    
    /**
     * 
     * @param \Furniture\ProductBundle\Entity\ProductPartMaterialOptionValue $value
     * @return \Furniture\ProductBundle\Entity\ProductPartMaterialVariant
     */
    public function addValue(ProductPartMaterialOptionValue $value){
        if(!$this->hasValue($value)){
            $this->values->add($value);
        }
        return $this;
    }

    /**
     * 
     * @param \Furniture\ProductBundle\Entity\ProductPartMaterialOptionValue $value
     * @return \Furniture\ProductBundle\Entity\ProductPartMaterialVariant
     */
    public function removeValue(ProductPartMaterialOptionValue $value){
        if($this->hasValue($value)){
            $this->values->removeElement($value);
        }
        return $this;
    }

        /**
     * Set available status
     *
     * @param bool $available
     *
     * @return ProductPartMaterialVariant
     */
    public function setAvailable($available)
    {
        $this->available = (bool) $available;

        return $this;
    }

    /**
     * Get available
     *
     * @return bool
     */
    public function isAvailable()
    {
        return $this->available;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ProductPartMaterialVariant
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

    /**
     * 
     * @return string
     */
    public function getDescriptionalName()
    {
        return $this->descriptionalName;
    }
    
    /**
     * 
     * @param string $name
     * @return \Furniture\ProductBundle\Entity\ProductPartMaterialVariant
     */
    public function setDescriptionalName($name)
    {
        $this->descriptionalName = $name;
        return $this;
    }

    /**
     * 
     * @return string
     */
    public function getDescriptionalCode()
    {
        return $this->descriptionalCode;
    }

    /**
     * 
     * @param string $code
     * @return \Furniture\ProductBundle\Entity\ProductPartMaterialVariant
     */
    public function setDescriptionalCode($code)
    {
        $this->descriptionalCode = $code;
        return $this;
    }

        /**
     * Generate name from values
     *
     * @return ProductPartMaterialVariant
     */
    public function generateNameFromValues()
    {
        $parts = [];

        foreach ($this->values as $value) {
            $parts[] = $value->getValue();
        }
        
        $prefix = [];
        if( !empty($this->getDescriptionalName()) )
            $prefix[] = $this->getDescriptionalName();
        if(!empty($this->getDescriptionalCode()))
            $prefix[] = $this->getDescriptionalCode();
        
        $this->name = implode( '-',$prefix).' '.implode(', ', $parts);

        return $this;
    }

    /**
     * Implement __toString
     *
     * @return string
     */
    public function __toString()
    {
        return $this->name ?: '';
    }
}
