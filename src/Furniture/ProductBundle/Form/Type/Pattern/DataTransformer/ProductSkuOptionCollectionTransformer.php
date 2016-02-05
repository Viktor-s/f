<?php

namespace Furniture\ProductBundle\Form\Type\Pattern\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class ProductSkuOptionCollectionTransformer implements DataTransformerInterface
{
    /**
     * @var \Furniture\SkuOptionBundle\Entity\SkuOptionVariant[]
     */
    private $options;

    /**
     * Construct
     *
     * @param \Furniture\SkuOptionsBundle\Entity\SkuOptionVariant[]
     */
    public function __construct($options)
    {
        $this->options = $options;
    }

    /**
     * {@inheritDoc}
     */
    public function transform($value)
    {
        $transformedData = [];

        $searchSkuOption = function ($id) use ($value) {
            /** @var \Furniture\SkuOptionBundle\Entity\SkuOptionVariant $item */
            foreach ($value as $item) {
                if ($item->getId() == $id) {
                    return $item;
                }
            }

            return null;
        };

        foreach ($this->options as $skuOption) {
            $name = $skuOption->getName();
            $id = $skuOption->getId();

            if (!isset($transformedData[$name])) {
                $transformedData[$name] = [];
            }

            $transformedData[$name][$id] = $searchSkuOption($id);
        }

        return $transformedData;
    }

    /**
     * {@inheritDoc}
     */
    public function reverseTransform($value)
    {
        $skuOptions = new ArrayCollection();

        foreach ($value as $groupName => $items) {
            foreach ($items as $skuOption) {
                if ($skuOption) {
                    $skuOptions[] = $skuOption;
                }
            }
        }

        return $skuOptions;
    }
}
