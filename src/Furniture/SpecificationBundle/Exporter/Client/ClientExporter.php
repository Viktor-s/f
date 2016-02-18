<?php

namespace Furniture\SpecificationBundle\Exporter\Client;

use Furniture\SpecificationBundle\Entity\Specification;
use Furniture\SpecificationBundle\Entity\SpecificationItem;
use Furniture\SpecificationBundle\Exporter\AbstractExporter;
use PHPExcel_Shared_Drawing;

class ClientExporter extends AbstractExporter
{
    /**
     * Export specification
     *
     * @param Specification     $specification
     * @param FieldMapForClient $fieldMap
     *
     * @return \PHPExcel
     */
    public function export(Specification $specification, FieldMapForClient $fieldMap)
    {
        $excel = $this->createPhpExcel($specification);
        $excel->setActiveSheetIndex(0);
        $sheet = $excel->getActiveSheet();

        $this->createHeader($sheet, $specification, 1);

        $row = 14;
        $this->createTableHeader($sheet, $fieldMap, $row);

        $positions = [];

        foreach ($specification->getItems() as $item) {
            if ($skuItem = $item->getSkuItem()) {
                $this->createRowDataForSkuItem($sheet, $item, $fieldMap, $row, $positions);

            } else if ($customItem = $item->getCustomItem()) {
                $this->createRowDataForCustomItem($sheet, $item, $fieldMap, $row, $positions);

            } else {
                throw new \RuntimeException(
                    sprintf(
                        'The specification with identifier "%s" has a empty item with identifier "%s".',
                        $specification->getId(),
                        $item->getId()
                    )
                );
            }
        }

        $totalColumns = count($positions);

        if ($fieldMap->hasFieldName() && $totalColumns > 3) {
            $this->createVolumeCell($sheet, $specification, $positions['name'], $row);
        }

        if ($fieldMap->hasFieldNotes() && $totalColumns > 3) {
            $this->createWeightCell($sheet, $specification, $positions['notes'], $row);
        }

        // Generate total
        if ($fieldMap->hasFieldTotalPrice()) {
            $this->createTotalRows($sheet, $specification, $positions['total_price'], $row);
        }

        $this->writeEmptyValuesForCells($sheet, $totalColumns, $row);

        $this->formatTable($sheet, $totalColumns, $row);

        return $excel;
    }

    /**
     * Create table row data for SKU item
     *
     * @param \PHPExcel_Worksheet $sheet
     * @param SpecificationItem   $item
     * @param FieldMapForClient   $fieldMap
     * @param int                 &$row
     * @param array               &$positions
     */
    private function createRowDataForSkuItem(
        \PHPExcel_Worksheet $sheet,
        SpecificationItem $item,
        FieldMapForClient $fieldMap,
        &$row,
        &$positions
    )
    {
        $productVariant = $item->getSkuItem()->getProductVariant();

        /** @var \Furniture\ProductBundle\Entity\Product $product */
        $product = $productVariant->getProduct();

        $column = 1;

        if ($fieldMap->hasFieldNumber()) {
            $positions['position'] = $column;
            $key = $this->generateCellKey($column++, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($item->getPosition());
            $this->formatPositionCell($cell);
        }

        if ($fieldMap->hasFieldPhoto()) {
            $positions['photo'] = $column;
            $key = $this->generateCellKey($column++, $row);

            $image = $productVariant->getImage();

            if ($image && $image->getPath()) {
                $obj = $this->createImageForExcel($image->getPath(), $key);
                $obj->setWorksheet($sheet);
            }

            $imageCell = $sheet->getCell($key);
            $this->formatImageCell($imageCell);
        }

        if ($fieldMap->hasFieldBrand()) {
            $positions['brand'] = $column;
            $key = $this->generateCellKey($column++, $row);
            $factory = $product->getFactory();
            $cell = $sheet->getCell($key);
            $cell->setValue($factory->getName());
            $this->formatBrandCell($cell);
        }

        if ($fieldMap->hasFieldName()) {
            $positions['name'] = $column;
            $key = $this->generateCellKey($column++, $row);
            $cell = $sheet->getCell($key);

            $values = [
                $product->getName(),
                $product->getFactoryCode(),
            ];

            if (count($product->getTypes()) > 0) {
                /** @var \Furniture\ProductBundle\Entity\Type $type */
                $type = $product->getTypes()->first();
                /** @var \Furniture\ProductBundle\Entity\TypeTranslation $translation */
                $translation = $type->translate();
                $values[] = $translation->getName();
            }

            $values = array_filter($values);

            $cell->setValue(implode("\n", $values));
            $this->formatNameCell($cell);
        }

        if ($fieldMap->hasFieldOptions()) {
            $positions['options'] = $column;
            $key = $this->generateCellKey($column++, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($this->generateVariantCharacteristics($productVariant));
            $this->formatOptionsCell($cell);
        }

        if ($fieldMap->hasFieldNotes()) {
            $positions['notes'] = $column;
            $key = $this->generateCellKey($column++, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($item->getNote());
            $this->formatNotesCell($cell);
        }

        if ($fieldMap->hasFieldQuantity()) {
            $positions['quantity'] = $column;
            $key = $this->generateCellKey($column++, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($item->getQuantity());
            $this->formatQuantityCell($cell);
        }

        if ($fieldMap->hasFieldPrice()) {
            $positions['price'] = $column;
            $key = $this->generateCellKey($column++, $row);
            $price = round($this->priceCalculator->calculateForProductVariant($productVariant) / 100, 2);
            $cell = $sheet->getCell($key);
            $cell->setValue($price);
            $this->formatPriceCell($cell);
        }

        if ($fieldMap->hasFieldTotalPrice()) {
            $positions['total_price'] = $column;
            $key = $this->generateCellKey($column, $row);
            $price = round($this->priceCalculator->calculateTotalForSpecificationItem($item) / 100, 2);
            $cell = $sheet->getCell($key);
            $cell->setValue($price);
            $this->formatTotalPriceCell($cell);
        }

        $this->formatSpecificationItemRowData($sheet, $column, $row);

        $row++;
    }

    /**
     * Create table row data for custom item
     *
     * @param \PHPExcel_Worksheet $sheet
     * @param SpecificationItem   $item
     * @param FieldMapForClient   $fieldMap
     * @param int                 &$row
     */
    private function createRowDataForCustomItem(
        \PHPExcel_Worksheet $sheet,
        SpecificationItem $item,
        FieldMapForClient $fieldMap,
        &$row,
        &$positions
    )
    {
        $column = 1;
        $customItem = $item->getCustomItem();

        if ($fieldMap->hasFieldNumber()) {
            $positions['position'] = $column;
            $key = $this->generateCellKey($column++, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($item->getPosition());
            $this->formatPositionCell($cell);
        }

        if ($fieldMap->hasFieldPhoto()) {
            $positions['photo'] = $column;
            $key = $this->generateCellKey($column++, $row);

            $image = $customItem->getImage();

            if ($image && $image->getPath()) {
                $obj = $this->createImageForExcel($image->getPath(), $key);
                $obj->setWorksheet($sheet);
            }

            $imageCell = $sheet->getCell($key);
            $this->formatImageCell($imageCell);
        }

        if ($fieldMap->hasFieldBrand()) {
            $positions['brand'] = $column;
            $key = $this->generateCellKey($column++, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($customItem->getFactoryName());
            $this->formatBrandCell($cell);
        }

        if ($fieldMap->hasFieldName()) {
            $positions['name'] = $column;
            $key = $this->generateCellKey($column++, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($customItem->getName());
            $this->formatNameCell($cell);
        }

        if ($fieldMap->hasFieldOptions()) {
            $positions['options'] = $column;
            $key = $this->generateCellKey($column++, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($customItem->getOptions());
            $this->formatOptionsCell($cell);
        }

        if ($fieldMap->hasFieldNotes()) {
            $positions['notes'] = $column;
            $key = $this->generateCellKey($column++, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($item->getNote());
            $this->formatNotesCell($cell);
        }

        if ($fieldMap->hasFieldQuantity()) {
            $positions['quantity'] = $column;
            $key = $this->generateCellKey($column++, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($item->getQuantity());
            $this->formatQuantityCell($cell);
        }

        if ($fieldMap->hasFieldPrice()) {
            $positions['price'] = $column;
            $key = $this->generateCellKey($column++, $row);
            $cell = $sheet->getCell($key);
            $price = round($item->getCustomItem()->getPrice() / 100, 2);
            $cell->setValue($price);
            $this->formatPriceCell($cell);
        }

        if ($fieldMap->hasFieldTotalPrice()) {
            $positions['total_price'] = $column;
            $key = $this->generateCellKey($column, $row);
            $totalPrice = round($this->priceCalculator->calculateTotalForSpecificationItem($item) / 100, 2);
            $cell = $sheet->getCell($key);
            $cell->setValue($totalPrice);
            $this->formatTotalPriceCell($cell);
        }

        $this->formatSpecificationItemRowData($sheet, $column, $row);

        $row++;
    }

    /**
     * Create weight cell
     *
     * @param \PHPExcel_Worksheet $sheet
     * @param Specification       $specification
     * @param int                 $position
     * @param int                 $row
     */
    private function createWeightCell(\PHPExcel_Worksheet $sheet, Specification $specification, $position, $row)
    {
        if ($position > 1) {
            $key = $this->generateCellKey($position - 1, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($this->translator->trans('specification.excel.weight'));
            $this->formatTotalTitlesCell($cell);
        }

        $key = $this->generateCellKey($position, $row);
        $cell = $sheet->getCell($key);
        $cell->setValue($specification->getWeight());
        $this->setAlignmentForCell($cell, 'left', 'top');
    }

    /**
     * Create weight cell
     *
     * @param \PHPExcel_Worksheet $sheet
     * @param Specification       $specification
     * @param int                 $position
     * @param int                 $row
     */
    private function createVolumeCell(\PHPExcel_Worksheet $sheet, Specification $specification, $position, $row)
    {
        if ($position > 1) {
            $key = $this->generateCellKey($position - 1, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($this->translator->trans('specification.excel.volume'));
            $this->formatTotalTitlesCell($cell);
        }

        $key = $this->generateCellKey($position, $row);
        $cell = $sheet->getCell($key);
        $cell->setValue($specification->getVolume());
        $this->setAlignmentForCell($cell, 'left', 'top');
    }

    /**
     * Create total rows
     *
     * @param \PHPExcel_Worksheet $sheet
     * @param Specification       $specification
     * @param int                 $totalPricePosition
     * @param int                 &$row
     */
    private function createTotalRows(
        \PHPExcel_Worksheet $sheet,
        Specification $specification,
        $totalPricePosition,
        &$row
    )
    {
        $useTitleCells = $totalPricePosition > 1;
        $startRow = $row;
        $startColumn = $useTitleCells ? $totalPricePosition - 1 : $totalPricePosition;

        if ($useTitleCells) {
            // Append the titles
            $key = $this->generateCellKey($totalPricePosition - 1, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($this->translator->trans('specification.excel.total'));
            $this->formatTotalTitlesCell($cell);
        }

        $totalPriceWithoutSale = round(
            $this->priceCalculator->calculateForSpecification($specification, false) / 100,
            2
        );
        $totalPriceWithSale = round($this->priceCalculator->calculateForSpecification($specification) / 100, 2);

        $key = $this->generateCellKey($totalPricePosition, $row);
        $cell = $sheet->getCell($key);
        $cell->setValue($totalPriceWithoutSale);
        $this->formatTotalPriceCell($cell);

        if ($totalPriceWithSale != $totalPriceWithoutSale) {
            $priceWithSales = $totalPriceWithoutSale;

            foreach ($specification->getSales() as $sale) {
                $row++;
                if ($useTitleCells) {
                    $key = $this->generateCellKey($totalPricePosition - 1, $row);
                    $cell = $sheet->getCell($key);
                    $cell->setValue(
                        $this->translator->trans(
                            'specification.excel.discount',
                            [
                                ':sale' => $sale->getSale(),
                            ]
                        )
                    );
                    $this->formatTotalTitlesCell($cell);
                }

                $key = $this->generateCellKey($totalPricePosition, $row);
                $salePrice = ($priceWithSales / 100) * $sale->getSale();
                $priceWithSales = $priceWithSales - $salePrice;
                $cell = $sheet->getCell($key);
                $cell->setValue($salePrice);
                $this->formatTotalPriceCell($cell);
            }

            $row++;

            if ($useTitleCells) {
                $key = $this->generateCellKey($totalPricePosition - 1, $row);
                $cell = $sheet->getCell($key);
                $cell->setValue($this->translator->trans('specification.excel.final'));
                $this->formatTotalTitlesCell($cell);
            }

            $key = $this->generateCellKey($totalPricePosition, $row);
            $cell = $sheet->getCell($key);
            $cell->setValue($totalPriceWithSale);
            $this->formatTotalPriceCell($cell);
            $cell->getStyle()->getFont()->setBold(true);
        }

        $key = $this->generateDiapasonKey($startColumn, $startRow, $totalPricePosition, $row);
        $style = $sheet->getStyle($key);
        $style
            ->applyFromArray(
                [
                    'borders' => [
                        'allborders' => [
                            'style' => \PHPExcel_Style_Border::BORDER_THIN,
                            'color' => [
                                'rgb' => '000000',
                            ],
                        ],
                    ],
                ]
            );

        $row++;
    }

    /**
     * Create header
     *
     * @param \PHPExcel_Worksheet $sheet
     * @param Specification       $specification
     * @param int                 &$row
     */
    private function createHeader(\PHPExcel_Worksheet $sheet, Specification $specification, $row)
    {
        $startRow = $row;
        $firstColumnIndex = 3;
        $secondColumnIndex = 5;
        $thirdColumnIndex = 8;
        $creator = $specification->getCreator();
        $retailer = $creator->getRetailerProfile();

        // Generate first column
        // Document number
        $cell = $this->mergeDiapason($sheet, 1, $row, 2, $row);
        $cell->setValue($specification->getDocumentNumber());
        $this->formatHeaderValueCell($cell);
        $this->setAlignmentForCell($cell, 'left', 'top');

        // Create at
        $key = $this->generateCellKey(3, $row);
        $cell = $sheet->getCell($key);
        $cell->setValue($specification->getCreatedAt()->format('Y/m/d H:i'));
        $this->formatHeaderValueCell($cell);
        $this->setAlignmentForCell($cell, 'center', 'top');

        $row++;
        $row++;

        // Client row
        $this->createHeaderRow(
            $sheet,
            1,
            $firstColumnIndex,
            $row,
            $this->translator->trans('specification.excel.client_name'),
            $specification->getBuyer() ? $specification->getBuyer()->getFullName() : 'None'
        );

        $row++;

        // Contact information
        $contacts = [];
        if ($specification->getBuyer()) {
            if ($specification->getBuyer()->getEmail()) {
                $contacts[] = $specification->getBuyer()->getEmail();
            }

            if ($specification->getBuyer()->getPhone()) {
                $contacts[] = $specification->getBuyer()->getPhone();
            };
        }

        $this->createHeaderRow(
            $sheet,
            1,
            $firstColumnIndex,
            $row,
            $this->translator->trans('specification.excel.contacts'),
            implode(' ', $contacts)
        );

        $row++;

        // Name
        $this->createHeaderRow(
            $sheet,
            1,
            $firstColumnIndex,
            $row,
            $this->translator->trans('specification.excel.name'),
            $specification->getName()
        );

        // Generate second column
        if ($retailer && $retailer->getLogoImage()) {
            $path = $retailer->getLogoImage()->getPath();

            if ($path) {
                $this->mergeDiapason($sheet, $firstColumnIndex + 1, $startRow, $secondColumnIndex, $row);
                $key = $this->generateCellKey($firstColumnIndex + 1, $startRow);
                $imageWidth = 150;
                $imageHeight = 100;
                $cellDimension = ceil(PHPExcel_Shared_Drawing::pixelsToCellDimension($imageWidth, $this->defaultFont));
                $rowHeight = ceil(PHPExcel_Shared_Drawing::pixelsToPoints($imageHeight));
                $obj = $this->createImageForExcel(
                    $path,
                    $key,
                    's150x100',
                    $imageWidth,
                    $imageHeight,
                    $cellDimension,
                    $rowHeight
                );
                $obj->setWorksheet($sheet);
                $imageCell = $sheet->getCell($key);

                $this->formatImageCell($imageCell, $cellDimension, $rowHeight);
                $this->setAlignmentForCell($imageCell, 'center', 'top');
            }
        }

        $row = $startRow;

        // Generate third column
        // Retailer name
        $this->createHeaderRow(
            $sheet,
            $secondColumnIndex + 1,
            $thirdColumnIndex,
            $row,
            $retailer->getName(),
            null
        );

        // Manager
        $this->createHeaderRow(
            $sheet,
            $secondColumnIndex + 1,
            $thirdColumnIndex,
            $row,
            $this->translator->trans('specification.excel.manager'),
            $creator->getUser()->getFullName()
        );

        $row++;

        // Address
        $this->createHeaderRow(
            $sheet,
            $secondColumnIndex + 1,
            $thirdColumnIndex,
            $row,
            $this->translator->trans('specification.excel.address'),
            $retailer->getAddress()
        );

        $row++;

        // Contacts
        $this->createHeaderRow(
            $sheet,
            $secondColumnIndex + 1,
            $thirdColumnIndex,
            $row,
            $this->translator->trans('specification.excel.contact_info'),
            [
                'Email: '.implode(', ', $retailer->getEmails()),
                'Toll-free: '.implode(', ', $retailer->getPhones()),
            ]
        );
    }

    /**
     * Create header row
     *
     * @param \PHPExcel_Worksheet $sheet
     * @param int                 $startColumn
     * @param int                 $endColumn
     * @param int                 $row
     * @param string              $label
     * @param string              $value
     */
    private function createHeaderRow(\PHPExcel_Worksheet $sheet, $startColumn, $endColumn, &$row, $label, $value)
    {
        $this->mergeDiapason($sheet, $startColumn, $row, $endColumn, $row);
        $key = $this->generateCellKey($startColumn, $row);
        $cell = $sheet->getCell($key);
        $cell->setValue($label);
        $this->formatHeaderLabelCell($cell);

        $row++;

        if (!is_array($value)) {
            $value = [$value];
        }

        foreach ($value as $item) {
            $this->createHeaderRowValue($sheet, $startColumn, $endColumn, $row, $item);
        }
    }

    /**
     * Create header row value
     *
     * @param \PHPExcel_Worksheet $sheet
     * @param int                 $startColumn
     * @param int                 $row
     * @param int                 $endColumn
     * @param int                 $row
     * @param string              $value
     */
    private function createHeaderRowValue(\PHPExcel_Worksheet $sheet, $startColumn, $endColumn, &$row, $value)
    {
        $this->mergeDiapason($sheet, $startColumn, $row, $endColumn, $row);
        $key = $this->generateCellKey($startColumn, $row);
        $cell = $sheet->getCell($key);
        $cell->setValue($value);
        $this->formatHeaderValueCell($cell);

        $row++;
    }

    /**
     * Create table header
     *
     * @param \PHPExcel_Worksheet $sheet
     * @param FieldMapForClient   $fieldMap
     * @param int                 $row
     */
    private function createTableHeader(\PHPExcel_Worksheet $sheet, FieldMapForClient $fieldMap, &$row)
    {
        $index = 1;

        if ($fieldMap->hasFieldNumber()) {
            $key = $this->generateCellKey($index++, $row);
            $sheet->setCellValue($key, '#');
        }

        if ($fieldMap->hasFieldPhoto()) {
            $key = $this->generateCellKey($index++, $row);
            $sheet->setCellValue($key, $this->translator->trans('specification.excel.photo'));
        }

        if ($fieldMap->hasFieldBrand()) {
            $key = $this->generateCellKey($index++, $row);
            $sheet->setCellValue($key, $this->translator->trans('specification.excel.factory'));
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
            $key = $this->generateCellKey($index++, $row);
            $sheet->setCellValue($key, $this->translator->trans('specification.excel.price'));
        }

        if ($fieldMap->hasFieldTotalPrice()) {
            $key = $this->generateCellKey($index, $row);
            $sheet->setCellValue($key, $this->translator->trans('specification.excel.total_price'));
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
    private function formatSpecificationItemRowData(\PHPExcel_Worksheet $sheet, $endColumn, $row)
    {
        $key = $this->generateDiapasonKey(1, $row, $endColumn, $row);
        $rowStyle = $sheet->getStyle($key);

        $rowStyle->applyFromArray(
            [
                'borders' => [
                    'allborders' => [
                        'style' => \PHPExcel_Style_Border::BORDER_THIN,
                        'color' => [
                            'rgb' => '000000',
                        ],
                    ],
                ],
            ]
        );
    }

    /**
     * Format position cell
     *
     * @param \PHPExcel_Cell $cell
     */
    private function formatPositionCell(\PHPExcel_Cell $cell)
    {
        $cell->getStyle()->applyFromArray(
            [
                'alignment' => [
                    'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                    'vertical'   => \PHPExcel_Style_Alignment::VERTICAL_TOP,
                ],
            ]
        );
    }

    /**
     * Format image cell
     *
     * @param \PHPExcel_Cell $cell
     * @param int            $cellWidth
     * @param int            $rowHeight
     */
    private function formatImageCell(
        \PHPExcel_Cell $cell,
        $cellWidth = self::IMAGE_COLUMN_WIDTH,
        $rowHeight = self::IMAGE_ROW_HEIGHT
    )
    {
        $row = $cell->getRow();
        $column = $cell->getColumn();
        $sheet = $cell->getWorksheet();
        $sheet->getRowDimension($row)->setRowHeight($rowHeight);
        $sheet->getColumnDimension($column)->setWidth($cellWidth);

        $this->setAlignmentForCell($cell, 'center', 'center');
    }

    /**
     * Format brand cell
     *
     * @param \PHPExcel_Cell $cell
     */
    private function formatBrandCell(\PHPExcel_Cell $cell)
    {
        $this->setAutoWidthForColumnByCell($cell);
        $this->setAlignmentForCell($cell, 'center', 'top');
    }

    /**
     * Format name cell
     *
     * @param \PHPExcel_Cell $cell
     */
    private function formatNameCell(\PHPExcel_Cell $cell)
    {
        $this->setAutoWidthForColumnByCell($cell);
        $this->setAlignmentForCell($cell, 'center', 'top');
    }

    /**
     * Format options cell
     *
     * @param \PHPExcel_Cell $cell
     */
    private function formatOptionsCell(\PHPExcel_Cell $cell)
    {
        $this->setAutoWidthForColumnByCell($cell);
        $this->setAlignmentForCell($cell, 'left', 'top');
    }

    /**
     * Format notes cell
     *
     * @param \PHPExcel_Cell $cell
     */
    private function formatNotesCell(\PHPExcel_Cell $cell)
    {
        $this->setAutoWidthForColumnByCell($cell);
        $this->setAlignmentForCell($cell, 'left', 'top');
    }

    /**
     * Format quantity cell
     *
     * @param \PHPExcel_Cell $cell
     */
    private function formatQuantityCell(\PHPExcel_Cell $cell)
    {
        $this->setAutoWidthForColumnByCell($cell);
        $this->setAlignmentForCell($cell, 'center', 'top');
    }

    /**
     * Format price cell
     *
     * @param \PHPExcel_Cell $cell
     */
    private function formatPriceCell(\PHPExcel_Cell $cell)
    {
        $this->setAutoWidthForColumnByCell($cell);
        $this->setAlignmentForCell($cell, 'center', 'top');
        $cell->setDataType(\PHPExcel_Cell_DataType::TYPE_NUMERIC);
        $cell->getStyle()->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
    }

    /**
     * Format total price cell
     *
     * @param \PHPExcel_Cell $cell
     */
    private function formatTotalPriceCell(\PHPExcel_Cell $cell)
    {
        $this->setAutoWidthForColumnByCell($cell);
        $this->setAlignmentForCell($cell, 'center', 'top');
        $cell->setDataType(\PHPExcel_Cell_DataType::TYPE_NUMERIC);
        $cell->getStyle()->getNumberFormat()->setFormatCode(\PHPExcel_Style_NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE);
    }

    /**
     * Format total titles cell
     *
     * @param \PHPExcel_Cell $cell
     */
    private function formatTotalTitlesCell(\PHPExcel_Cell $cell)
    {
        $this->setAlignmentForCell($cell, 'right', 'top');
        $cell->getStyle()->getFont()->setBold(true);
    }

    /**
     * Format header label
     *
     * @param \PHPExcel_Cell $cell
     */
    private function formatHeaderLabelCell(\PHPExcel_Cell $cell)
    {
        $this->setAlignmentForCell($cell, 'left', 'top');
        $cell->getStyle()->getFont()->setBold(true);
    }

    /**
     * Format header value cell
     *
     * @param \PHPExcel_Cell $cell
     */
    private function formatHeaderValueCell(\PHPExcel_Cell $cell)
    {
        $this->setAlignmentForCell($cell, 'left', 'top');
    }
}
