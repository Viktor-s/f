<?php

namespace Furniture\SpecificationBundle\Exporter;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\SpecificationBundle\Entity\Specification;
use Furniture\SpecificationBundle\Exporter\Client\FieldMapForClient;
use Furniture\SpecificationBundle\Exporter\Factory\FieldMapForFactory;
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
     * @param string            $format
     *
     * @return \PHPExcel_Writer_IWriter
     */
    public function exportForClient(Specification $specification, FieldMapForClient $fieldMap, $format);

    /**
     * Export for factory
     *
     * @param Specification      $specification
     * @param FieldMapForFactory $fieldMap
     * @param Factory            $factory
     * @param string             $format
     *
     * @return \PHPExcel_Writer_IWriter
     */
    public function exportForFactory(
        Specification $specification,
        FieldMapForFactory $fieldMap,
        Factory $factory,
        $format
    );

    /**
     * Export for custom
     *
     * @param Specification               $specification
     * @param FieldMapForFactory          $fieldMap
     * @param GroupedCustomItemsByFactory $grouped
     * @param string                      $format
     *
     * @return \PHPExcel_Writer_IWriter
     */
    public function exportForCustom(
        Specification $specification,
        FieldMapForFactory $fieldMap,
        GroupedCustomItemsByFactory $grouped,
        $format
    );
}
