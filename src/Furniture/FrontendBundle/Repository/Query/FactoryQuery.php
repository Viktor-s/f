<?php

namespace Furniture\FrontendBundle\Repository\Query;

use Sylius\Component\Core\Model\Taxon;

class FactoryQuery
{
    /**
     * @var array
     */
    private $ids = [];

    /**
     * @var array|Taxon[]
     */
    private $taxons = [];

    /**
     * With id
     *
     * @param int $id
     *
     * @return FactoryQuery
     */
    public function withId($id)
    {
        $this->ids[$id] = $id;

        return $this;
    }

    /**
     * With ids
     *
     * @param array $ids
     *
     * @return FactoryQuery
     */
    public function withIds(array $ids)
    {
        $this->ids = [];

        foreach ($ids as $id) {
            $this->withId($id);
        }

        return $this;
    }

    /**
     * Has ids?
     *
     * @return bool
     */
    public function hasIds()
    {
        return count($this->ids) > 0;
    }

    /**
     * Get ids
     *
     * @return array
     */
    public function getIds()
    {
        return array_values($this->ids);
    }

    /**
     * With taxon
     *
     * @param Taxon $taxon
     *
     * @return FactoryQuery
     */
    public function withTaxon(Taxon $taxon)
    {
        if (!isset($this->taxons[$taxon->getId()])) {
            $this->taxons[$taxon->getId()] = $taxon;
        }

        return $this;
    }

    /**
     * With taxons
     *
     * @param array|Taxon[] $taxons
     *
     * @return FactoryQuery
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
}
