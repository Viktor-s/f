<?php

namespace Furniture\FrontendBundle\Repository\Query;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\PostBundle\Entity\Post;

class PostQuery
{
    /**
     * @var Factory[]
     */
    private $factories = [];

    /**
     * @var array
     */
    private $types = [];

    /**
     * @var bool
     */
    private $onlyPublished = true;

    /**
     * With factory
     *
     * @param Factory $factory
     *
     * @return PostQuery
     */
    public function withFactory(Factory $factory)
    {
        if (!isset($this->factories[$factory->getId()])) {
            $this->factories[$factory->getId()] = $factory;
        }

        return $this;
    }

    /**
     * With factories
     *
     * @param Factory[] $factories
     *
     * @return PostQuery
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
     * @return Factory[]
     */
    public function getFactories()
    {
        return array_values($this->factories);
    }

    /**
     * With type
     *
     * @param int $type
     *
     * @return PostQuery
     */
    public function withType($type)
    {
        $availableTypes = [Post::TYPE_NEWS, Post::TYPE_CIRCULAR];

        if (!in_array($type, $availableTypes)) {
            throw new \InvalidArgumentException(sprintf(
                'Invalid type "%s". Available types: "%s".',
                $type,
                implode('", "', $availableTypes)
            ));
        }

        if (!isset($this->types[$type])) {
            $this->types[$type] = $type;
        }

        return $this;
    }

    /**
     * With types
     *
     * @param array $types
     *
     * @return PostQuery
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
     * @return bool
     */
    public function getTypes()
    {
        return array_values($this->types);
    }

    /**
     * With news type
     *
     * @return PostQuery
     */
    public function withNews()
    {
        $this->withType(Post::TYPE_NEWS);

        return $this;
    }

    /**
     * With circular type
     *
     * @return PostQuery
     */
    public function withCircular()
    {
        $this->withType(Post::TYPE_CIRCULAR);

        return $this;
    }

    /**
     * With only published
     *
     * @return PostQuery
     */
    public function withOnlyPublished()
    {
        $this->onlyPublished = true;

        return $this;
    }

    /**
     * Without only published
     *
     * @return PostQuery
     */
    public function withoutOnlyPublished()
    {
        $this->onlyPublished = false;

        return $this;
    }

    /**
     * Is only published?
     *
     * @return bool
     */
    public function isOnlyPublished()
    {
        return $this->onlyPublished;
    }
}