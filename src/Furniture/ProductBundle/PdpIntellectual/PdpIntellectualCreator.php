<?php

namespace Furniture\ProductBundle\PdpIntellectual;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\ProductBundle\Entity\PdpIntellectualCompositeExpression;
use Furniture\ProductBundle\Entity\PdpIntellectualElement;
use Furniture\ProductBundle\Entity\PdpIntellectualRoot;
use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Entity\ProductPdpInput;

class PdpIntellectualCreator
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Construct
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Create a pdp intellectual tree from array
     *
     * @param PdpIntellectualRoot $root
     * @param array               $tree
     */
    public function createFromArray(PdpIntellectualRoot $root, array $tree)
    {
        if (!$root->getProduct()) {
            throw new \RuntimeException('Can not create intellectual pdp without product.');
        }

        $rootExpression = $this->parseExpression($root->getProduct(), $tree, 'root');

        $root->setExpression($rootExpression);
    }

    /**
     * Parse expression
     *
     * @param Product $product
     * @param array   $expressionInfo
     * @param string  $path
     *
     * @return PdpIntellectualCompositeExpression
     */
    private function parseExpression(Product $product, array $expressionInfo, $path)
    {
        $type = $this->getTypeOfExpression($expressionInfo, $path);

        $expression = new PdpIntellectualCompositeExpression();
        $expression->setType($type);

        if (isset($expressionInfo['appendText'])) {
            $expression->setAppendText($expressionInfo['appendText']);
        }

        if (isset($expressionInfo['prependText'])) {
            $expression->setAppendText($expressionInfo['prependText']);
        }

        if (isset($expressionInfo['child'])) {
            // Create child elements
            foreach ($expressionInfo['child'] as $key => $childExpressionInfo) {
                $childPath = sprintf('%s[%s]', $path, $key);
                $childExpression = $this->parseExpression($product, $childExpressionInfo, $childPath);

                $expression->addChild($childExpression);
            }
        }

        if (!empty($expressionInfo['elements'])) {
            // Create input elements
            foreach ($expressionInfo['elements'] as $elementInfo) {
                $inputId = $elementInfo['input'];

                $pdpInput = $this->em->find(ProductPdpInput::class, $inputId);

                if (!$pdpInput) {
                    throw new \InvalidArgumentException(sprintf(
                        'Not found PDP Input with identifier "%s".',
                        $inputId
                    ));
                }

                $element = new PdpIntellectualElement();
                $element->setInput($pdpInput);

                if (!empty($elementInfo['appendText'])) {
                    $element->setAppendText($elementInfo['appendText']);
                }

                if (!empty($elementInfo['prependText'])) {
                    $element->setPrependText($elementInfo['prependText']);
                }

                $expression->addElement($element);
            }
        }

        return $expression;
    }

    /**
     * Get type of expression
     *
     * @param array  $expression
     * @param string $path
     *
     * @return int
     */
    private function getTypeOfExpression(array $expression, $path)
    {
        if (!isset($expression['type'])) {
            throw new \InvalidArgumentException('Missing "type" in expression');
        }

        $type = $expression['type'];

        $availableTypes = [
            PdpIntellectualCompositeExpression::TYPE_AND,
            PdpIntellectualCompositeExpression::TYPE_OR,
        ];

        if (!in_array($type, $availableTypes)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid type "%s" of expression at path "%s".',
                $type,
                $path
            ));
        }

        return (int)$type;
    }
}
