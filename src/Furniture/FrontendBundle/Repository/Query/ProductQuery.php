<?php

namespace Furniture\FrontendBundle\Repository\Query;

use Furniture\FactoryBundle\Entity\Factory;
use Sylius\Component\Core\Model\Taxon;

class ProductQuery
{
    /**
     * @var array|Taxon[]
     */
    private $taxons = [];

    /**
     * @var array|Factory[]
     */
    private $factories = [];

    /**
     * With taxon
     *
     * @param Taxon $taxon
     *
     * @return ProductQuery
     */
    public function withTaxon(Taxon $taxon)
    {
        $this->taxons[spl_object_hash($taxon)] = $taxon;

        return $this;
    }

    /**
     * With many taxons
     *
     * @param array|Taxon[] $taxons
     *
     * @return ProductQuery
     */
    public function withTaxons(array $taxons)
    {
        $this->taxons = [];

        foreach ($taxons as $taxon) {
            $this->withTaxon($taxon);
        }

        return $this;
    }

    /**
     * Has taxons?
     *
     * @return bool
     */
    public function hasTaxons()
    {
        return count($this->taxons) > 0;
    }

    /**
     * Get taxons
     *
     * @return array|Taxon[]
     */
    public function getTaxons()
    {
        return array_values($this->taxons);
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
}
