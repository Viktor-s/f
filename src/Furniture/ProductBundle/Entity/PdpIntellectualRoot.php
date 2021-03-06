<?php

namespace Furniture\ProductBundle\Entity;

class PdpIntellectualRoot
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var Product
     */
    private $product;

    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $graphJson;

    /**
     * @var array
     */
    private $treeJson;
    
    /**
     * @var array
     */
    private $treePathForInputsJson;
    
    /**
     * @var PdpIntellectualCompositeExpression
     */
    private $expression;

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
     * @return PdpIntellectualRoot
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
     * Set name
     *
     * @param string $name
     *
     * @return PdpIntellectualRoot
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
     * Set expression
     *
     * @param PdpIntellectualCompositeExpression $expression
     *
     * @return PdpIntellectualRoot
     */
    public function setExpression(PdpIntellectualCompositeExpression $expression)
    {
        $this->expression = $expression;

        return $this;
    }

    /**
     * Get expression
     *
     * @return PdpIntellectualCompositeExpression
     */
    public function getExpression()
    {
        return $this->expression;
    }

    /**
     * @return mixed
     */
    public function getGraphJson()
    {
        return $this->graphJson;
    }

    /**
     * @param mixed $graphJson
     * @return PdpIntellectualRoot
     */
    public function setGraphJson($graphJson)
    {
        $this->graphJson = $graphJson;

        return $this;
    }
    
    /**
     * @return mixed
     */
    public function getTreeJson()
    {
        return $this->treeJson;
    }

    /**
     * @param mixed $graphJson
     * @return PdpIntellectualRoot
     */
    public function setTreeJson($treeJson)
    {
        $this->treeJson = $treeJson;

        return $this;
    }

    /**
     * 
     * @param array $treePathForInputsJson
     * @return \Furniture\ProductBundle\Entity\PdpIntellectualRoot
     */
    public function setTreePathForInputsJson($treePathForInputsJson){
        $this->treePathForInputsJson = $treePathForInputsJson;
        return $this;
    }
    
    /**
     * 
     * @return array
     */
    public function getTreePathForInputsJson(){
        return $this->treePathForInputsJson;
    }
    
    /**
     * 
     * @param integer $id
     * @return array|null
     */
    public function getTreePathForInputJson($id){
        if( array_key_exists($id, $this->treePathForInputsJson) )
                return $this->treePathForInputsJson[$id];
        
        return null;
    }
    
    /**
     * 
     * @param integer $id
     * @param array $path
     * @return \Furniture\ProductBundle\Entity\PdpIntellectualRoot
     */
    public function updateTreePathForInputJson($id, $path){
        $this->treePathForInputsJson[$id] = $path;
        return $this;
    }
    
}
