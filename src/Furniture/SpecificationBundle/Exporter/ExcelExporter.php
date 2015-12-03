<?php

namespace Furniture\SpecificationBundle\Exporter;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\PricingBundle\Calculator\PriceCalculator;
use Furniture\ProductBundle\Entity\ProductVariant;
use Furniture\SpecificationBundle\Entity\Specification;
use Furniture\SpecificationBundle\Exporter\Client\ClientExcelExporter;
use Furniture\SpecificationBundle\Exporter\Client\FieldMap;
use Furniture\SpecificationBundle\Model\GroupedCustomItemsByFactory;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Component\Translation\TranslatorInterface;
use Sylius\Bundle\CurrencyBundle\Templating\Helper\MoneyHelper;
use Sylius\Component\Currency\Context\CurrencyContextInterface;

class ExcelExporter implements ExporterInterface
{
    /**
     * @var TranslatorInterface
     */
    protected $translator;

    /**
     * @var PriceCalculator
     */
    protected $priceCalculator;

    /**
     * @var DataManager
     */
    protected $imagineDataManager;

    /**
     * @var FilterManager
     */
    protected $filterManager;

    /**
     * @var MoneyHelper
     */
    protected $moneyHelper;

    /**
     * @var CurrencyContextInterface
     */
    protected $currencyContext;
    
    /**
     * Construct
     *
     * @param TranslatorInterface $translator
     * @param PriceCalculator     $priceCalculator
     * @param DataManager         $imagineDataManager
     * @param FilterManager       $filterManager
     */
    public function __construct(
        TranslatorInterface $translator,
        PriceCalculator $priceCalculator,
        DataManager $imagineDataManager,
        FilterManager $filterManager
    )
    {
        $this->translator = $translator;
        $this->priceCalculator = $priceCalculator;
        $this->imagineDataManager = $imagineDataManager;
        $this->filterManager = $filterManager;
    }

    /**
     * {@inheritDoc}
     */
    public function exportForClient(Specification $specification, FieldMap $fieldMap)
    {
        $internalExporter = new ClientExcelExporter(
            $this->translator,
            $this->priceCalculator,
            $this->imagineDataManager,
            $this->filterManager
        );

        return $internalExporter->export($specification, $fieldMap);
    }

    /**
     * {@inheritDoc}
     */
    public function exportForFactory(Specification $specification, FieldMapForFactory $fieldMap, Factory $factory)
    {
        $excel = $this->createPhpExcel($specification);
        $excel->setActiveSheetIndex(0);
        $activeSheet = $excel->getActiveSheet();

        // Create headers
        $index = 1;
        $tableHeadIndex = 1;
        if ($fieldMap->hasFieldNumber()) {
            $key = $this->generateCellKey($index++, $tableHeadIndex);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.number'));
        }

        if ($fieldMap->hasFieldPhoto()) {
            $key = $this->generateCellKey($index++, $tableHeadIndex);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.photo'));
        }
        
        if ($fieldMap->hasFieldName()) {
            $key = $this->generateCellKey($index++, $tableHeadIndex);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.name'));
        }

        if ($fieldMap->hasFieldSku()) {
            $key = $this->generateCellKey($index++, $tableHeadIndex);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.sku'));
        }

        if ($fieldMap->hasFieldNote()) {
            $key = $this->generateCellKey($index++, $tableHeadIndex);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.description'));
        }

        if ($fieldMap->hasFieldSize()) {
            $key = $this->generateCellKey($index++, $tableHeadIndex);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.size'));
        }

        if ($fieldMap->hasFieldQuantity()) {
            $key = $this->generateCellKey($index++, $tableHeadIndex);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.quantity'));
        }

        $countColumns = $index;
        $rowIndex = 2;
        $numberOfRecords = 1;

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

            $cellIndex = 1;

            if ($fieldMap->hasFieldNumber()) {
                $key = $this->generateCellKey($cellIndex++, $rowIndex);
                $activeSheet->setCellValue($key, $numberOfRecords);
            }

            if ($fieldMap->hasFieldPhoto()) {
                $key = $this->generateCellKey($cellIndex++, $rowIndex);
                $image = $productVariant->getImage();
                if ($image && $image->getPath()) {
                    $activeSheet->getRowDimension($rowIndex)->setRowHeight(80);
                    $obj = $this->createImageForExcel($image->getPath(), $key);
                    $obj->setWorksheet($activeSheet);
                }
            }

            if ($fieldMap->hasFieldName()) {
                $key = $this->generateCellKey($cellIndex++, $rowIndex);
                $activeSheet->setCellValue($key, $productVariant->getProduct()->getName());
            }
            
            if ($fieldMap->hasFieldSku()) {
                $key = $this->generateCellKey($cellIndex++, $rowIndex);
                $activeSheet->setCellValue($key, $productVariant->getSku());
            }

            if ($fieldMap->hasFieldNote()) {
                $key = $this->generateCellKey($cellIndex++, $rowIndex);
                $activeSheet->setCellValue($key, $item->getNote());
            }

            if ($fieldMap->hasFieldSize()) {
                $key = $this->generateCellKey($cellIndex++, $rowIndex);
                $activeSheet->setCellValue($key, $productVariant->getHumanSize());
            }

            if ($fieldMap->hasFieldQuantity()) {
                $key = $this->generateCellKey($cellIndex++, $rowIndex);
                $activeSheet->setCellValue($key, $item->getQuantity());
            }

            $rowIndex++;
        }

        $writer = new \PHPExcel_Writer_Excel2007($excel);

        return $writer;
    }

    /**
     * {@inheritDoc}
     */
    public function exportForCustom(
        Specification $specification,
        FieldMapForCustom $fieldMap,
        GroupedCustomItemsByFactory $grouped
    )
    {
        $excel = $this->createPhpExcel($specification);
        $excel->setActiveSheetIndex(0);
        $activeSheet = $excel->getActiveSheet();

        $index = 1;
        $tableHeadIndex = 1;
        if ($fieldMap->hasFieldNumber()) {
            $key = $this->generateCellKey($index++, $tableHeadIndex);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.number'));
        }

        if ($fieldMap->hasFieldPhoto()) {
            $key = $this->generateCellKey($index++, $tableHeadIndex);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.photo'));
        }

        if ($fieldMap->hasFieldName()) {
            $key = $this->generateCellKey($index++, $tableHeadIndex);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.name'));
        }

        if ($fieldMap->hasFieldQuantity()) {
            $key = $this->generateCellKey($index++, $tableHeadIndex);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.quantity'));
        }

        $countColumns = $index;
        $rowIndex = 2;
        $numberOfRecords = 1;

        foreach ($grouped->getItems() as $item) {
            $cellIndex = 1;

            if ($fieldMap->hasFieldNumber()) {
                $key = $this->generateCellKey($cellIndex++, $rowIndex);
                $activeSheet->setCellValue($key, $numberOfRecords++);
            }

            if ($fieldMap->hasFieldPhoto()) {                
                $key = $this->generateCellKey($cellIndex++, $rowIndex);
                $image = $item->getCustomItem()->getImage();
                if ($image && $image->getPath()) {
                    $activeSheet->getRowDimension($rowIndex)->setRowHeight(80);
                    $obj = $this->createImageForExcel($image->getPath(), $key);
                    $obj->setWorksheet($activeSheet);
                }
                
            }

            if ($fieldMap->hasFieldName()) {
                $key = $this->generateCellKey($cellIndex++, $rowIndex);
                $activeSheet->setCellValue($key, $item->getCustomItem()->getName());
            }

            if ($fieldMap->hasFieldQuantity()) {
                $key = $this->generateCellKey($cellIndex++, $rowIndex);
                $activeSheet->setCellValue($key, $item->getQuantity());
            }

            $rowIndex++;
        }

        // Add header
        /*
        $startCell = $this->generateCellKey(1, 1);
        $endCell = $this->generateCellKey($countColumns, 1);
        $activeSheet->mergeCells($startCell . ':' . $endCell);
        $activeSheet->setCellValue($startCell, 'SPECIFICATION HEADER');

        $startCell = $this->generateCellKey(1, 2);
        $endCell = $this->generateCellKey($countColumns, 2);
        $activeSheet->mergeCells($startCell . ':' . $endCell);
        $activeSheet->setCellValue($startCell, sprintf(
            '#%d %s %s',
            $specification->getId(),
            $specification->getName(),
            $specification->getCreatedAt()->format('Y/m/d H:i')
        ));

        $startCell = $this->generateCellKey(1, 3);
        $endCell = $this->generateCellKey($countColumns, 3);
        $activeSheet->mergeCells($startCell . ':' . $endCell);
        $activeSheet->setCellValue($startCell, sprintf(
            '%s: %s',
            $this->translator->trans('specification.excel.factory'),
            $grouped->getFactoryName()
        ));*/

        $writer = new \PHPExcel_Writer_Excel2007($excel);

        return $writer;
    }

}
