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

    private function setHeader(Specification $specification, \PHPExcel_Worksheet $activeSheet)
    {
        /*Header Info*/
        if($retailerProfile = $specification->getCreator()->getRetailerProfile()){
            /*Set Logo*/
            if ($logoImage = $retailerProfile->getLogoImage()) {
                if ($logoImage->getPath()) {
                    $activeSheet->mergeCells('A1:B6');
                    $obj = $this->createImageForExcel($logoImage->getPath(), 'A1', 's201x203');
                    $obj->setWorksheet($activeSheet);
                }
            }
            /*Set retailer name*/
            $activeSheet->mergeCells('G1:I1');
            $activeSheet->setCellValue('G1', $retailerProfile->getName());
            /*Manager name*/
            $activeSheet->mergeCells('G2:I2');
            $activeSheet->setCellValue('G2', $specification->getCreator()->getUser()->getCustomer()->getFirstName()
                    .' '.$specification->getCreator()->getUser()->getCustomer()->getLastName());
            /*Retailer address*/
            $activeSheet->mergeCells('G3:I3');
            $activeSheet->setCellValue('G1', $retailerProfile->getAddress());
            /*Retailer phone*/
            $activeSheet->mergeCells('G4:I4');
            $activeSheet->setCellValue('G4', implode( ',', $retailerProfile->getPhones()), true)->getStyle()->getAlignment()->setWrapText(true);
            /*Manager email address*/
            $activeSheet->mergeCells('G5:I5');
            $activeSheet->setCellValue('G4', $specification->getCreator()->getUser()->getCustomer()->getEmail());
            /*Specification creation date*/
            $activeSheet->setCellValue('B7', $this->translator->trans('specification.excel.creation_date').': '.$specification->getCreatedAt()->format('Y-m-d'));
            /*Specification client*/
            if($client = $specification->getBuyer()){
                /*Client name*/
                $activeSheet->mergeCells('B8:C8');
                $activeSheet->setCellValue('B8', $this->translator->trans('specification.excel.client_name').': '.$client);
                /*Client address*/
                $activeSheet->mergeCells('B9:C9');
                $activeSheet->setCellValue('B9', $client->getAddress());
                /*Client phone*/
                $activeSheet->mergeCells('B10:C10');
                $activeSheet->setCellValue('B10', $client->getPhone());
            }
            /*Specification description*/
            $activeSheet->mergeCells('B11:D12');
            $activeSheet->setCellValue('B11', $specification->getDescription());
        }
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
