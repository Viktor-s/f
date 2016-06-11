<?php

namespace Furniture\ProductBundle\PdpIntellectual;

use Furniture\ProductBundle\Entity\PdpIntellectualCompositeExpression;
use Furniture\ProductBundle\Entity\PdpIntellectualElement;
use Furniture\ProductBundle\Entity\PdpIntellectualRoot;

class PdpIntellectualConverter
{
    /**
     * Convert root pdp intellectual to array
     *
     * @param PdpIntellectualRoot $root
     *
     * @return array
     */
    public function convertToArray(PdpIntellectualRoot $root)
    {
        $rootExpression = $root->getExpression();

        return $this->convertCompositeExpression($rootExpression);
    }

    /**
     * Convert expression to array
     *
     * @param PdpIntellectualCompositeExpression $expression
     *
     * @return array
     */
    private function convertCompositeExpression(PdpIntellectualCompositeExpression $expression)
    {
        $data = [
            'type'        => $expression->getType(),
            'appendText'  => $expression->getAppendText(),
            'prependText' => $expression->getPrependText(),
            'pdpInput'    => $expression->getPdpInput() ? $expression->getPdpInput()->getId() : null,
            'child'       => [],
            'id'          => $expression->getId(),
            'elements'    => [],
        ];

        if ($expression->getChild()->count()) {
            foreach ($expression->getChild() as $child) {
                $data['child'][] = $this->convertCompositeExpression($child);
            }
        }

        if ($expression->getElements()->count()) {
            foreach ($expression->getElements() as $element) {
                $data['elements'][] = $this->convertElementToArray($element);
            }
        }

        return $data;
    }

    /**
     * Convert element to array
     *
     * @param PdpIntellectualElement $element
     *
     * @return array
     */
    private function convertElementToArray(PdpIntellectualElement $element)
    {
        return [
            'input'       => $element->getInput()->getId(),
            'appendText'  => $element->getAppendText(),
            'prependText' => $element->getPrependText(),
        ];
    }
}
