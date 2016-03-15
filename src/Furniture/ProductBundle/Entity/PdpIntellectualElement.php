<?php

namespace Furniture\ProductBundle\Entity;

class PdpIntellectualElement
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var PdpIntellectualCompositeExpression
     */
    private $expression;

    /**
     * @var ProductPdpInput
     */
    private $input;

    /**
     * @var string
     */
    private $prependText;

    /**
     * @var string
     */
    private $appendText;

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
     * Set expression
     *
     * @param PdpIntellectualCompositeExpression $expression
     *
     * @return PdpIntellectualElement
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
     * Set input
     *
     * @param ProductPdpInput $input
     *
     * @return PdpIntellectualElement
     */
    public function setInput(ProductPdpInput $input)
    {
        $this->input = $input;

        return $this;
    }

    /**
     * Get input
     *
     * @return ProductPdpInput
     */
    public function getInput()
    {
        return $this->input;
    }

    /**
     * Set prepend text
     *
     * @param string $text
     *
     * @return PdpIntellectualElement
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
     * @return PdpIntellectualElement
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
