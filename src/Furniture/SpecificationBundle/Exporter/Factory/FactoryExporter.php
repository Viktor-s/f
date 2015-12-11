<?php

namespace Furniture\SpecificationBundle\Exporter\Factory;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\ProductBundle\Entity\Product;
use Furniture\ProductBundle\Entity\ProductVariant;
use Furniture\SpecificationBundle\Entity\Specification;
use Furniture\SpecificationBundle\Entity\SpecificationItem;

class FactoryExporter extends AbstractFactoryExporter
{
    /**
     * Export for factory
     *
     * @param Specification      $specification
     * @param FieldMapForFactory $fieldMap
     * @param Factory            $factory
     *
     * @return \PHPExcel
     */
    public function export(Specification $specification, FieldMapForFactory $fieldMap, Factory $factory)
    {
        $excel = $this->createPhpExcel($specification);
        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();

        $row = 1;
        $this->createTableHeader($sheet, $fieldMap, $row);
        $this->exportTableData($sheet, $specification, $fieldMap, $factory, $row);

        return $excel;
    }

    /**
     * {@inheritDoc}
     */
    protected function exportTableData(
        \PHPExcel_Worksheet $sheet,
        Specification $specification,
        FieldMapForFactory $fieldMap,
        Factory $factory,
        &$row
    )
    {
        $numberOfRecord = 1;

        foreach ($specification->getItems() as $item) {
            if (!$item->getSkuItem()) {
                continue;
            }

            $productVariant = $item->getSkuItem()->getProductVariant();
            /** @var \Furniture\ProductBundle\Entity\Product $product */
            $product = $productVariant->getProduct();
            $productFactory = $product->getFactory();

            if ($productFactory->getId() != $factory->getId()) {
                continue;
            }

            $this->exportTableRowData($sheet, $item, $product, $productVariant, $fieldMap, $row, $numberOfRecord);
        }
    }

    /**
     * Export table row data
     *
     * @param \PHPExcel_Worksheet $sheet
     * @param SpecificationItem   $specificationItem
     * @param Product             $product
     * @param ProductVariant      $productVariant
     * @param FieldMapForFactory  $fieldMap
     * @param int                 &$row
     * @param int                 &$numberOfRecords
     */
    private function exportTableRowData(
        \PHPExcel_Worksheet $sheet,
        SpecificationItem $specificationItem,
        Product $product,
        ProductVariant $productVariant,
        FieldMapForFactory $fieldMap,
        &$row,
        &$numberOfRecords
    )
    {
        $index = 1;

        if ($fieldMap->hasFieldNumber()) {
            $key = $this->generateCellKey($index++, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($numberOfRecords);
            $this->formatPositionCell($cell);
        }

        if ($fieldMap->hasFieldType()) {
            $key = $this->generateCellKey($index++, $row);
            $cell = $sheet->getCell($key);
            $value = '';

            if (count($product->getTypes()) > 0) {
                /** @var \Furniture\ProductBundle\Entity\Type $type */
                $type = $product->getTypes()->first();
                /** @var \Furniture\ProductBundle\Entity\TypeTranslation $translate */
                $translate = $type->translate();

                $value = $cell->setValue($translate->getName());
            }

            $cell->setValue($value);
            $this->formatTypeCell($cell);
        }

        if ($fieldMap->hasFieldFactoryCode()) {
            $key = $this->generateCellKey($index++, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($product->getFactoryCode());
            $this->formatFactoryCodeCell($cell);

        }

        if ($fieldMap->hasFieldName()) {
            $key = $this->generateCellKey($index++, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($product->getName());
            $this->formatNameCell($cell);
        }

        if ($fieldMap->hasFieldOptions()) {
            $key = $this->generateCellKey($index++, $row);
            $cell = $sheet->getCell($key);
            $options = $this->generateVariantCharacteristics($productVariant);
            $cell->setValue($options);
            $this->formatOptionsCell($cell);
        }

        if ($fieldMap->hasFieldNotes()) {
            $key = $this->generateCellKey($index++, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($specificationItem->getNote());
            $this->formatNotesCell($cell);
        }

        if ($fieldMap->hasFieldQuantity()) {
            $key = $this->generateCellKey($index++, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($specificationItem->getQuantity());
            $this->formatQuantityCell($cell);
        }

        if ($fieldMap->hasFieldPrice()) {
            $key = $this->generateCellKey($index, $row);
            $cell = $sheet->getCell($key);
            // @todo: get via calculator
            $price = round($productVariant->getPrice() / 100, 2);
            $cell->setValue($price);
            $this->formatPriceCell($cell);
        }

        $this->formatSpecificationItemRowData($sheet, $index, $row);

        $row++;
        $numberOfRecords++;
    }
}
