<?php

namespace Furniture\ProductBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Furniture\ProductBundle\Entity\ProductPdpInput;

class PdpIntellectualCompositeExpression
{
    const TYPE_AND = 1;
    const TYPE_OR = 2;

    /**
     * @var int
     */
    private $id;

    /**
     *
     * @var \Furniture\ProductBundle\Entity\ProductPdpInput
     */
    private $pdpInput;

    /**
     * @var PdpIntellectualCompositeExpression
     */
    private $parent;

    /**
     * @var int
     */
    private $type;

    /**
     * The children composite expression.
     *
     * @var Collection
     */
    private $child;

    /**
     * @var Collection|PdpIntellectualElement[]
     */
    private $elements;

    /**
     * @var string
     */
    private $prependText;

    /**
     * @var string
     */
    private $appendText;

    /**
     * Construct
     */
    public function __construct()
    {
        $this->child = new ArrayCollection();
        $this->elements = new ArrayCollection();
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
     * Set parent
     *
     * @param PdpIntellectualCompositeExpression $compositeExpression
     *
     * @return PdpIntellectualCompositeExpression
     */
    public function setParent(PdpIntellectualCompositeExpression $compositeExpression = null)
    {
        $this->parent = $compositeExpression;

        return $this;
    }

    /**
     * 
     * @return \Furniture\ProductBundle\Entity\ProductPdpInput
     */
    public function getPdpInput(){
        return $this->pdpInput;
    }
    
    /**
     * 
     * @param ProductPdpInput $pdpInput
     * @return \Furniture\ProductBundle\Entity\PdpIntellectualCompositeExpression
     */
    public function setPdpInput(ProductPdpInput $pdpInput){
        $pdpInput;
        $this->pdpInput = $pdpInput;
        return $this;
    }


    /**
     * Get parent
     *
     * @return PdpIntellectualCompositeExpression $compositeExpression
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Set type
     *
     * @param int $type
     *
     * @return PdpIntellectualCompositeExpression
     */
    public function setType($type)
    {
        $availableTypes = [
            self::TYPE_AND,
            self::TYPE_OR
        ];

        if (!in_array($type, $availableTypes)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid expression type "%s". Available types: 1 - AND, 2 - OR',
                $type
            ));
        }

        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return int
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Is and?
     *
     * @return bool
     */
    public function isAnd()
    {
        return $this->type === self::TYPE_AND;
    }

    /**
     * Is or?
     *
     * @return bool
     */
    public function isOr()
    {
        return $this->type === self::TYPE_OR;
    }

    /**
     * Has child?
     *
     * @param PdpIntellectualCompositeExpression $expression
     *
     * @return bool
     */
    public function hasChild(PdpIntellectualCompositeExpression $expression)
    {
        return $this->child->exists(function ($key, PdpIntellectualCompositeExpression $item) use ($expression) {
            if (!$expression->getId()) {
                return false;
            }

            return $expression->getId() == $item->getId();
        });
    }

    /**
     * Add child
     *
     * @param PdpIntellectualCompositeExpression $expression
     *
     * @return PdpIntellectualCompositeExpression
     */
    public function addChild(PdpIntellectualCompositeExpression $expression)
    {
        if (!$this->hasChild($expression)) {
            $expression->setParent($this);

            $this->child->add($expression);
        }

        return $this;
    }

    /**
     * Remove child expression
     *
     * @param PdpIntellectualCompositeExpression $expression
     *
     * @return PdpIntellectualCompositeExpression
     */
    public function removeChild(PdpIntellectualCompositeExpression $expression)
    {
        $removalKey = null;

        $this->child->forAll(function ($key, PdpIntellectualCompositeExpression $item) use ($expression, &$removalKey) {
            if ($item->getId() == $expression->getId()) {
                $removalKey = $key;

                return false;
            }

            return true;
        });

        if (null !== $removalKey) {
            $this->child->remove($removalKey);
        }

        return $this;
    }

    /**
     * Get children expressions
     *
     * @return Collection|PdpIntellectualCompositeExpression[]
     */
    public function getChild()
    {
        return $this->child;
    }

    /**
     * Has element?
     *
     * @param PdpIntellectualElement $element
     *
     * @return bool
     */
    public function hasElement(PdpIntellectualElement $element)
    {
        return $this->elements->exists(function ($key, PdpIntellectualElement $item) use ($element) {
            if (!$element->getId()) {
                return;
            }

            return $element->getId() == $item->getId();
        });
    }

    /**
     * Add element
     *
     * @param PdpIntellectualElement $element
     *
     * @return PdpIntellectualCompositeExpression
     */
    public function addElement(PdpIntellectualElement $element)
    {
        if (!$this->hasElement($element)) {
            $element->setExpression($this);

            $this->elements->add($element);
        }

        return $this;
    }

    /**
     * Remove element
     *
     * @param PdpIntellectualElement $element
     *
     * @return PdpIntellectualCompositeExpression
     */
    public function removeElement(PdpIntellectualElement $element)
    {
        $removalKey = null;

        $this->elements->forAll(function ($key, PdpIntellectualElement $item) use ($element, &$removalKey) {
            if ($item->getId() == $element->getId()) {
                $removalKey = $key;

                return false;
            }

            return true;
        });

        if (null !== $removalKey) {
            $this->elements->remove($removalKey);
        }

        return $this;
    }

    /**
     * Get elements
     *
     * @return Collection|PdpIntellectualElement
     */
    public function getElements()
    {
        return $this->elements;
    }

    /**
     * Set prepend text
     *
     * @param string $text
     *
     * @return PdpIntellectualCompositeExpression
     */
    public function setPrependText($text)
    {
        $this->prependText = $text;

        return $this;
    }

    /**
     * Get prepend text
     *
     * @return string
     */
    public function getPrependText()
    {
        return $this->prependText;
    }

    /**
     * Set append text
     *
     * @param string $text
     *
     * @return PdpIntellectualCompositeExpression
     */
    public function setAppendText($text)
    {
        $this->appendText = $text;

        return $this;
    }

    /**
     * Get append text
     *
     * @return string
     */
    public function getAppendText()
    {
        return $this->appendText;
    }
}
