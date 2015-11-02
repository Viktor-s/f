<?php

namespace Furniture\SpecificationBundle\Exporter;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\SpecificationBundle\Entity\Specification;
use Furniture\SpecificationBundle\Model\GroupedCustomItemsByFactory;

/**
 * All specification exporters should implement this interface
 */
interface ExporterInterface
{
    /**
     * Export full specification
     *
     * @param Specification     $specification
     * @param FieldMapForClient $fieldMap
     *
     * @return mixed
     */
    public function exportForClient(Specification $specification, FieldMapForClient $fieldMap);

    /**
     * Export for factory
     *
     * @param Specification      $specification
     * @param FieldMapForFactory $fieldMap
     * @param Factory            $factory
     *
     * @return mixed
     */
    public function exportForFactory(Specification $specification, FieldMapForFactory $fieldMap, Factory $factory);

    /**
     * Export for custom
     *
     * @param Specification               $specification
     * @param FieldMapForCustom           $fieldMap
     * @param GroupedCustomItemsByFactory $grouped
     *
     * @return mixed
     */
    public function exportForCustom(
        Specification $specification,
        FieldMapForCustom $fieldMap,
        GroupedCustomItemsByFactory $grouped
    );
}
