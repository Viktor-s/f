<?php

namespace Furniture\ProductBundle\Model;

/**
 * Collection for save all product part and variant selection.
 *
 * Attention: this collection is readonly!
 */
class ProductPartMaterialVariantSelectionCollection implements \Iterator, \Countable
{
    /**
     * @var array|ProductPartMaterialVariantSelection[]
     */
    private $variants = [];

    /**
     * Construct
     *
     * @param array|ProductPartMaterialVariantSelection[] $variants
     */
    public function __construct(array $variants)
    {
        foreach ($variants as $variant) {
            if (!$variant instanceof ProductPartMaterialVariantSelection) {
                throw new \InvalidArgumentException(sprintf(
                    'Invalid variant selection. Must be a "%s" instance, but "%s" given.',
                    ProductPartMaterialVariantSelection::class,
                    is_object($variant) ? get_class($variant) : gettype($variant)
                ));
            }

            $this->variants[] = $variant;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function current()
    {
        return current($this->variants);
    }

    /**
     * {@inheritDoc}
     */
    public function next()
    {
        return next($this->variants);
    }

    /**
     * {@inheritDoc}
     */
    public function key()
    {
        return key($this->variants);
    }

    /**
     * {@inheritDoc}
     */
    public function valid()
    {
        return key($this->variants) !== null;
    }

    /**
     * {@inheritDoc}
     */
    public function rewind()
    {
        reset($this->variants);
    }

    /**
     * {@inheritDoc}
     */
    public function count()
    {
        return count($this->variants);
    }
}
