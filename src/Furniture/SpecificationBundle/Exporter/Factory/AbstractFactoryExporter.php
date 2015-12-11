<?php

namespace Furniture\SpecificationBundle\Exporter\Factory;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\SpecificationBundle\Entity\Specification;
use Furniture\SpecificationBundle\Exporter\AbstractExporter;

abstract class AbstractFactoryExporter extends AbstractExporter
{
    /**
     * Create table header
     *
     * @param \PHPExcel_WorkSheet $sheet
     * @param FieldMapForFactory  $fieldMap
     * @param int                 &$row
     */
    protected function createTableHeader(\PHPExcel_Worksheet $sheet, FieldMapForFactory $fieldMap, &$row)
    {
        $index = 1;

        if ($fieldMap->hasFieldNumber()) {
            $key = $this->generateCellKey($index++, $row);
            $sheet->setCellValue($key, '#');
        }

        if ($fieldMap->hasFieldType()) {
            $key = $this->generateCellKey($index++, $row);
            $sheet->setCellValue($key, $this->translator->trans('specification.excel.type'));
        }

        if ($fieldMap->hasFieldFactoryCode()) {
            $key = $this->generateCellKey($index++, $row);
            $sheet->setCellValue($key, $this->translator->trans('specification.excel.factory_code'));
        }

        if ($fieldMap->hasFieldName()) {
            $key = $this->generateCellKey($index++, $row);
            $sheet->setCellValue($key, $this->translator->trans('specification.excel.name'));
        }

        if ($fieldMap->hasFieldOptions()) {
            $key = $this->generateCellKey($index++, $row);
            $sheet->setCellValue($key, $this->translator->trans('specification.excel.options'));
        }

        if ($fieldMap->hasFieldNotes()) {
            $key = $this->generateCellKey($index++, $row);
            $sheet->setCellValue($key, $this->translator->trans('specification.excel.notes'));
        }

        if ($fieldMap->hasFieldQuantity()) {
            $key = $this->generateCellKey($index++, $row);
            $sheet->setCellValue($key, $this->translator->trans('specification.excel.quantity'));
        }

        if ($fieldMap->hasFieldPrice()) {
            $key = $this->generateCellKey($index, $row);
            $sheet->setCellValue($key, $this->translator->trans('specification.excel.price'));
        }

        // Generate style
        $coordinate = $this->generateDiapasonKey(1, $row, $index, $row);
        $this->formatTableHeader($sheet, $coordinate);

        $row++;
    }

    /**
     * Format row data for item
     *
     * @param \PHPExcel_Worksheet $sheet
     * @param int                 $endColumn
     * @param int                 $row
     */
    protected function formatSpecificationItemRowData(\PHPExcel_Worksheet $sheet, $endColumn, $row)
    {
        $key = $this->generateDiapasonKey(1, $row, $endColumn, $row);
        $rowStyle = $sheet->getStyle($key);

        $rowStyle->applyFromArray([
            'borders' => [
                'allborders' => [
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => [
                        'rgb' => '000000',
                    ],
                ],
            ],
        ]);
    }

    /**
     * Format position cell
     *
     * @param \PHPExcel_Cell $cell
     */
    protected function formatPositionCell(\PHPExcel_Cell $cell)
    {
        $this->setAlignmentForCell($cell, 'center', 'top');
        $this->setAutoWidthForColumnByCell($cell);
    }

    /**
     * Format type cell
     *
     * @param \PHPExcel_Cell $cell
     */
    protected function formatTypeCell(\PHPExcel_Cell $cell)
    {
        $this->setAlignmentForCell($cell, 'center', 'top');
        $this->setAutoWidthForColumnByCell($cell);
    }

    /**
     * Format factory code cell
     *
     * @param \PHPExcel_Cell $cell
     */
    protected function formatFactoryCodeCell(\PHPExcel_Cell $cell)
    {
        $this->setAlignmentForCell($cell, 'center', 'top');
        $this->setAutoWidthForColumnByCell($cell);
    }

    /**
     * Format name cell
     *
     * @param \PHPExcel_Cell $cell
     */
    protected function formatNameCell(\PHPExcel_Cell $cell)
    {
        $this->setAlignmentForCell($cell, 'left', 'top');
        $this->setAutoWidthForColumnByCell($cell);
    }

    /**
     * Format options cell
     *
     * @param \PHPExcel_Cell $cell
     */
    protected function formatOptionsCell(\PHPExcel_Cell $cell)
    {
        $this->setAlignmentForCell($cell, 'left', 'top');
        $this->setAutoWidthForColumnByCell($cell);
    }

    /**
     * Format quantity cell
     *
     * @param \PHPExcel_Cell $cell
     */
    protected function formatQuantityCell(\PHPExcel_Cell $cell)
    {
        $this->setAlignmentForCell($cell, 'center', 'top');
        $this->setAutoWidthForColumnByCell($cell);
    }

    /**
     * Format notes cell
     *
     * @param \PHPExcel_Cell $cell
     */
    protected function formatNotesCell(\PHPExcel_Cell $cell)
    {
        $this->setAlignmentForCell($cell, 'left', 'top');
        $this->setAutoWidthForColumnByCell($cell);
    }

    /**
     * Format price cell
     *
     * @param \PHPExcel_Cell $cell
     */
    protected function formatPriceCell(\PHPExcel_Cell $cell)
    {
        $this->setAlignmentForCell($cell, 'center', 'top');
        $this->setAutoWidthForColumnByCell($cell);
        $cell->setDataType(\PHPExcel_Cell_DataType::TYPE_NUMERIC);
        $cell->getStyle()->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
    }
}
