<?php

namespace Furniture\SpecificationBundle\Exporter\Factory;

use Furniture\SpecificationBundle\Entity\Specification;
use Furniture\SpecificationBundle\Entity\SpecificationItem;
use Furniture\SpecificationBundle\Model\GroupedCustomItemsByFactory;

class CustomFactoryExporter extends AbstractFactoryExporter
{
    /**
     * Export for factory
     *
     * @param Specification               $specification
     * @param FieldMapForFactory          $fieldMap
     * @param GroupedCustomItemsByFactory $groupedItems
     *
     * @return \PHPExcel
     */
    public function export(
        Specification $specification,
        FieldMapForFactory $fieldMap,
        GroupedCustomItemsByFactory $groupedItems
    )
    {
        $excel = $this->createPhpExcel($specification);
        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();

        $row = 1;
        $this->createTableHeader($sheet, $fieldMap, $row);
        $this->exportTableData($sheet, $fieldMap, $groupedItems, $row);

        return $excel;
    }

    /**
     * Export table data
     *
     * @param \PHPExcel_Worksheet         $sheet
     * @param FieldMapForFactory          $fieldMap
     * @param GroupedCustomItemsByFactory $groupedItems
     * @param integer                     &$row
     */
    protected function exportTableData(
        \PHPExcel_Worksheet $sheet,
        FieldMapForFactory $fieldMap,
        GroupedCustomItemsByFactory $groupedItems,
        &$row
    )
    {
        $numberOfRecords = 1;

        foreach ($groupedItems->getItems() as $customItem) {
            $this->exportTableRow($sheet, $fieldMap, $customItem, $row, $numberOfRecords);
        }
    }


    protected function exportTableRow(
        \PHPExcel_Worksheet $sheet,
        FieldMapForFactory $fieldMap,
        SpecificationItem $item,
        &$row,
        &$numberOfRecords
    )
    {
        $index = 1;
        $customItem = $item->getCustomItem();

        if ($fieldMap->hasFieldNumber()) {
            $key = $this->generateCellKey($index++, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($numberOfRecords);
            $this->formatPositionCell($cell);
        }

        if ($fieldMap->hasFieldType()) {
            $key = $this->generateCellKey($index++, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue('');
            $this->formatTypeCell($cell);
        }

        if ($fieldMap->hasFieldName()) {
            $key = $this->generateCellKey($index++, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($customItem->getName());
            $this->formatNameCell($cell);
        }

        if ($fieldMap->hasFieldOptions()) {
            $key = $this->generateCellKey($index++, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($customItem->getOptions());
            $this->formatOptionsCell($cell);
        }

        if ($fieldMap->hasFieldNotes()) {
            $key = $this->generateCellKey($index++, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($customItem->getOptions());
            $this->formatNotesCell($cell);
        }

        if ($fieldMap->hasFieldQuantity()) {
            $key = $this->generateCellKey($index++, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($item->getQuantity());
            $this->formatQuantityCell($cell);
        }

        if ($fieldMap->hasFieldPrice()) {
            $key = $this->generateCellKey($index, $row);
            $cell = $sheet->getCell($key);
            // @todo: get via calculator
            $cell->setValue(round($item->getPrice() / 100, 2));
            $this->formatPriceCell($cell);
        }

        $this->formatSpecificationItemRowData($sheet, $index, $row);

        $row++;
        $numberOfRecords++;
    }
}
