<?php

namespace Furniture\FrontendBundle\Repository\Query;

use Furniture\UserBundle\Entity\User;
use Furniture\CompositionBundle\Entity\CompositeCollection;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\ProductBundle\Entity\Category;
use Furniture\ProductBundle\Entity\Space;
use Furniture\ProductBundle\Entity\Style;
use Furniture\ProductBundle\Entity\Type;
use Furniture\RetailerBundle\Entity\RetailerProfile;

class ProductQuery
{
    /**
     * @var array|Space[]
     */
    private $spaces = [];

    /**
     * @var array|Category[]
     */
    private $categories = [];

    /**
     * @var array|Type[]
     */
    private $types = [];

    /**
     * @var array|Style[]
     */
    private $styles = [];

    /**
     * @var array|Factory[]
     */
    private $factories = [];

    /**
     * @var array|CompositeCollection[]
     */
    private $compositeCollections = [];

    /**
     * @var RetailerProfile
     */
    private $retailer;

    /**
     * @var bool
     */
    private $onlyAvailable = true;

    /**
     * @var bool
     */
    private $factoryEnabled = true;

    /**
     * With taxon
     *
     * @param Space $space
     *
     * @return ProductQuery
     */
    public function withSpace(Space $space)
    {
        $this->spaces[spl_object_hash($space)] = $space;

        return $this;
    }

    /**
     * With many spaces
     *
     * @param array|Space[] $spaces
     *
     * @return ProductQuery
     */
    public function withSpaces(array $spaces)
    {
        $this->spaces = [];

        foreach ($spaces as $space) {
            $this->withSpace($space);
        }

        return $this;
    }

    /**
     * Has spaces?
     *
     * @return bool
     */
    public function hasSpaces()
    {
        return count($this->spaces) > 0;
    }

    /**
     * Get spaces
     *
     * @return array|Space[]
     */
    public function getSpaces()
    {
        return array_values($this->spaces);
    }

    /**
     * With category
     *
     * @param Category $category
     *
     * @return ProductQuery
     */
    public function withCategory(Category $category)
    {
        $this->categories[$category->getId()] = $category;

        return $this;
    }

    /**
     * With categories
     *
     * @param array|Category[] $categories
     *
     * @return ProductQuery
     */
    public function withCategories(array $categories)
    {
        $this->categories = [];

        foreach ($categories as $category) {
            $this->withCategory($category);
        }

        return $this;
    }

    /**
     * Has categories?
     *
     * @return bool
     */
    public function hasCategories()
    {
        return count($this->categories) > 0;
    }

    /**
     * Get categories
     *
     * @return array|Category[]
     */
    public function getCategories()
    {
        return array_values($this->categories);
    }

    /**
     * With type
     *
     * @param Type $type
     *
     * @return ProductQuery
     */
    public function withType(Type $type)
    {
        $this->types[$type->getId()] = $type;

        return $this;
    }

    /**
     * With types
     *
     * @param array|Type[] $types
     *
     * @return ProductQuery
     */
    public function withTypes(array $types)
    {
        $this->types = [];

        foreach ($types as $type) {
            $this->withType($type);
        }

        return $this;
    }

    /**
     * Has types?
     *
     * @return bool
     */
    public function hasTypes()
    {
        return count($this->types) > 0;
    }

    /**
     * Get types
     *
     * @return Type[]
     */
    public function getTypes()
    {
        return array_values($this->types);
    }

    /**
     * With style
     *
     * @param Style $style
     *
     * @return ProductQuery
     */
    public function withStyle(Style $style)
    {
        $this->styles[$style->getId()] = $style;

        return $this;
    }

    /**
     * With styles
     *
     * @param array|Style[] $styles
     *
     * @return ProductQuery
     */
    public function withStyles(array $styles)
    {
        $this->styles = [];

        foreach ($styles as $style) {
            $this->withStyle($style);
        }

        return $this;
    }

    /**
     * Has styles?
     *
     * @return bool
     */
    public function hasStyles()
    {
        return count($this->styles) > 0;
    }

    /**
     * Get styles
     *
     * @return Style[]
     */
    public function getStyles()
    {
        return array_values($this->styles);
    }

    /**
     * With composite collection
     *
     * @param CompositeCollection $compositeCollection
     *
     * @return ProductQuery
     */
    public function withCompositeCollection(CompositeCollection $compositeCollection)
    {
        $this->compositeCollections[$compositeCollection->getId()] = $compositeCollection;

        return $this;
    }

    /**
     * With composite collections
     *
     * @param array|CompositeCollection[] $compositeCollections
     *
     * @return ProductQuery
     */
    public function withCompositeCollections(array $compositeCollections)
    {
        $this->compositeCollections = [];

        foreach ($compositeCollections as $compositeCollection) {
            $this->withCompositeCollection($compositeCollection);
        }

        return $this;
    }

    /**
     * Has composite collection?
     *
     * @return bool
     */
    public function hasCompositeCollections()
    {
        return count($this->compositeCollections) > 0;
    }

    /**
     * Get composite collections
     *
     * @return CompositeCollection[]
     */
    public function getCompositeCollections()
    {
        return $this->compositeCollections;
    }

    /**
     * With factory
     *
     * @param Factory $factory
     *
     * @return ProductQuery
     */
    public function withFactory(Factory $factory)
    {
        $this->factories[spl_object_hash($factory)] = $factory;

        return $this;
    }

    /**
     * With factories
     *
     * @param array|Factory[] $factories
     *
     * @return ProductQuery
     */
    public function withFactories(array $factories)
    {
        $this->factories = [];

        foreach ($factories as $factory) {
            $this->withFactory($factory);
        }

        return $this;
    }

    /**
     * Has factories?
     *
     * @return bool
     */
    public function hasFactories()
    {
        return count($this->factories) > 0;
    }

    /**
     * Get factories
     *
     * @return array|Factory[]
     */
    public function getFactories()
    {
        return array_values($this->factories);
    }

    /**
     * With retailer profile.
     *
     * @param RetailerProfile $retailer
     *
     * @return ProductQuery
     */
    public function withRetailer(RetailerProfile $retailer)
    {
        $this->retailer = $retailer;

        return $this;
    }

    /**
     * Has content user?
     *
     * @return bool
     */
    public function hasRetailer()
    {
        return (bool) $this->retailer;
    }

    /**
     * Get content user
     *
     * @return RetailerProfile
     */
    public function getRetailer()
    {
        return $this->retailer;
    }

    /**
     * With only available products
     *
     * @return ProductQuery
     */
    public function withOnlyAvailable()
    {
        $this->onlyAvailable = true;

        return $this;
    }

    /**
     * Without only available
     *
     * @return ProductQuery
     */
    public function withoutOnlyAvailable()
    {
        $this->onlyAvailable = false;

        return $this;
    }

    /**
     * Is only available?
     *
     * @return bool
     */
    public function isOnlyAvailable()
    {
        return $this->onlyAvailable;
    }

    /**
     * With factory enabled
     *
     * @return ProductQuery
     */
    public function withFactoryEnabled()
    {
        $this->factoryEnabled = true;

        return $this;
    }

    /**
     * Without factory enabled
     *
     * @return ProductQuery
     */
    public function withoutFactoryEnabled()
    {
        $this->factoryEnabled = false;

        return $this;
    }

    /**
     * Is factory enabled?
     *
     * @return bool
     */
    public function isFactoryEnabled()
    {
        return $this->factoryEnabled;
    }
}
