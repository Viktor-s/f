<?php

namespace Furniture\SpecificationBundle\Exporter;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\PricingBundle\Calculator\PriceCalculator;
use Furniture\ProductBundle\Entity\ProductVariant;
use Furniture\SpecificationBundle\Entity\Specification;
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
    private $translator;

    /**
     * @var PriceCalculator
     */
    private $priceCalculator;

    /**
     * @var DataManager
     */
    private $imagineDataManager;

    /**
     * @var FilterManager
     */
    private $filterManager;

    /**
     * Construct
     *
     * @param TranslatorInterface $translator
     * @param PriceCalculator     $priceCalculator
     */
    public function __construct(
        TranslatorInterface $translator,
        PriceCalculator $priceCalculator,
        DataManager $imagineDataManager,
        FilterManager $filterManager
    ) {
        $this->translator = $translator;
        $this->priceCalculator = $priceCalculator;
        $this->imagineDataManager = $imagineDataManager;
        $this->filterManager = $filterManager;
    }

    private function setHeader(Specification $specification, \PHPExcel_Worksheet $activeSheet)
    {
        /*Header Info*/
        if($retailerProfile = $specification->getCreator()->getRetailerUserProfile()->getRetailerProfile()){
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
            $activeSheet->setCellValue('G2', $specification->getCreator()->getCustomer()->getFirstName().' '.$specification->getCreator()->getCustomer()->getLastName());
            /*Retailer address*/
            $activeSheet->mergeCells('G3:I3');
            $activeSheet->setCellValue('G1', $retailerProfile->getAddress());
            /*Retailer phone*/
            $activeSheet->mergeCells('G4:I4');
            $activeSheet->setCellValue('G4', implode( ',', $retailerProfile->getPhones()), true)->getStyle()->getAlignment()->setWrapText(true);
            /*Manager email address*/
            $activeSheet->mergeCells('G5:I5');
            $activeSheet->setCellValue('G4', $specification->getCreator()->getCustomer()->getEmail());
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
    public function exportForClient(Specification $specification, FieldMapForClient $fieldMap)
    {
        $excel = $this->createPhpExcel($specification);
        $excel->setActiveSheetIndex(0);
        $activeSheet = $excel->getActiveSheet();

        //Set HEADER data
        $this->setHeader($specification, $activeSheet);
        
        // Create headers
        $index = 1;
        
        $theadIndex = 13;
        $activeSheet
                ->getStyle('A'.$theadIndex.':L'.$theadIndex)->getFill()
                ->applyFromArray(array(
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                    'startcolor' => array(
                    'rgb' => 'A9A9A9'
                    )
                ));
        if ($fieldMap->hasFieldNumber()) {
            $key = $this->generateCellKey($index++, $theadIndex);
            //$activeSheet->setCellValue($key, $this->translator->trans('specification.excel.number'));
        }

        if ($fieldMap->hasFieldFactory()) {
            $key = $this->generateCellKey($index++, $theadIndex);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.factory'));
        }

        if ($fieldMap->hasFieldPhoto()) {
            $key = $this->generateCellKey($index++, $theadIndex);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.photo'));
        }

        if ($fieldMap->hasFieldName()) {
            $key = $this->generateCellKey($index++, $theadIndex);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.name'));
        }

        if ($fieldMap->hasFieldArticle()) {
            $key = $this->generateCellKey($index++, $theadIndex);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.article'));
        }

        if ($fieldMap->hasFieldSize()) {
            $key = $this->generateCellKey($index++, $theadIndex);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.size'));
        }

        if ($fieldMap->hasFieldFinishes()) {
            $key = $this->generateCellKey($index++, $theadIndex);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.finishes'));
        }

        if ($fieldMap->hasFieldCharacteristics()) {
            $key = $this->generateCellKey($index++, $theadIndex);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.characteristics'));
        }

        if ($fieldMap->hasFieldQuantity()) {
            $key = $this->generateCellKey($index++, $theadIndex);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.quantity'));
        }

        if ($fieldMap->hasFieldPrice()) {
            $key = $this->generateCellKey($index++, $theadIndex);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.price'));
        }

        if ($fieldMap->hasFieldTotalPrice()) {
            $key = $this->generateCellKey($index, $theadIndex);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.total_price'));
        }

        $countColumns = $index;

        $rowIndex = 14;
        $numberOfRecords = 1;
        $activeSheet->getColumnDimension('C')->setWidth(18);
        $activeSheet->getColumnDimension('G')->setWidth(20);
        foreach ($specification->getItems() as $item) {
            $cellIndex = 1;
            $rowIndex ++;
            if ($skuItem = $item->getSkuItem()) {
                $productVariant = $skuItem->getProductVariant();
                /** @var \Furniture\ProductBundle\Entity\Product $product */
                $product = $productVariant->getProduct();

                if ($fieldMap->hasFieldNumber()) {
                    $key = $this->generateCellKey($cellIndex++, $rowIndex);
                    $activeSheet->setCellValue($key, $numberOfRecords++);
                }

                if ($fieldMap->hasFieldFactory()) {
                    $key = $this->generateCellKey($cellIndex++, $rowIndex);
                    $factory = $product->getFactory();
                    $activeSheet->setCellValue($key, $factory->getName());
                }

                if ($fieldMap->hasFieldPhoto()) {
                    $key = $this->generateCellKey($cellIndex++, $rowIndex);

                    $image = $item->getSkuItem()->getProductVariant()->getImage();

                    if ($image && $image->getPath()) {
                        $activeSheet->getRowDimension($rowIndex)->setRowHeight(80);
                        $obj = $this->createImageForExcel($image->getPath(), $key);
                        $obj->setWorksheet($activeSheet);
                    }
                }

                if ($fieldMap->hasFieldName()) {
                    $key = $this->generateCellKey($cellIndex++, $rowIndex);
                    $activeSheet->setCellValue($key, $product->getName());
                }

                if ($fieldMap->hasFieldArticle()) {
                    $key = $this->generateCellKey($cellIndex++, $rowIndex);
                    $activeSheet->setCellValue($key, $productVariant->getSku());
                }

                if ($fieldMap->hasFieldSize()) {
                    $key = $this->generateCellKey($cellIndex++, $rowIndex);
                    $activeSheet->setCellValue($key, $productVariant->getHumanSize());
                }

                if ($fieldMap->hasFieldCharacteristics()) {
                    $key = $this->generateCellKey($cellIndex++, $rowIndex);
                    $activeSheet->setCellValue($key, $this->generateVariantCharacteristics($productVariant));
                }

                if ($fieldMap->hasFieldFinishes()) {
                    $cellIndex++;
                }

                if ($fieldMap->hasFieldQuantity()) {
                    $key = $this->generateCellKey($cellIndex++, $rowIndex);
                    $activeSheet->setCellValue($key, $item->getQuantity());
                }

                if ($fieldMap->hasFieldPrice()) {
                    $key = $this->generateCellKey($cellIndex++, $rowIndex);
                    $price = $this->priceCalculator->calculateForProductVariant($productVariant);
                    $activeSheet->setCellValue($key, $price . ' EUR');
                }

                if ($fieldMap->hasFieldTotalPrice()) {
                    $key = $this->generateCellKey($cellIndex++, $rowIndex);
                    $price = $this->priceCalculator->calculateTotalForSpecificationItem($item);
                    $activeSheet->setCellValue($key, $price . ' EUR');
                }
            } else if ($customItem = $item->getCustomItem()) {
                if ($fieldMap->hasFieldNumber()) {
                    $key = $this->generateCellKey($cellIndex++, $rowIndex);
                    $activeSheet->setCellValue($key, $numberOfRecords++);
                }

                if ($fieldMap->hasFieldFactory()) {
                    $key = $this->generateCellKey($cellIndex++, $rowIndex);
                    $activeSheet->setCellValue($key, $customItem->getFactoryName());
                }

                if ($fieldMap->hasFieldPhoto()) {
                    $key = $this->generateCellKey($cellIndex++, $rowIndex);
                    $image = $customItem->getImage();
                    if ($image && $image->getPath()) {
                        $activeSheet->getRowDimension($rowIndex)->setRowHeight(80);
                        $obj = $this->createImageForExcel($image->getPath(), $key);
                        $obj->setWorksheet($activeSheet);
                    }
                }

                if ($fieldMap->hasFieldName()) {
                    $key = $this->generateCellKey($cellIndex++, $rowIndex);
                    $activeSheet->setCellValue($key, $customItem->getName());
                }

                if ($fieldMap->hasFieldArticle()) {
                    $cellIndex++;
                }

                if ($fieldMap->hasFieldSize()) {
                    $cellIndex++;
                }

                if ($fieldMap->hasFieldCharacteristics()) {
                    $key = $this->generateCellKey($cellIndex++, $rowIndex);
                    $activeSheet->setCellValue($key, $customItem->getOptions());
                }

                if ($fieldMap->hasFieldFinishes()) {
                    $cellIndex++;
                }

                if ($fieldMap->hasFieldQuantity()) {
                    $key = $this->generateCellKey($cellIndex++, $rowIndex);
                    $activeSheet->setCellValue($key, $item->getQuantity());
                }

                if ($fieldMap->hasFieldPrice()) {
                    $key = $this->generateCellKey($cellIndex++, $rowIndex);
                    $price = $customItem->getPrice();
                    $activeSheet->setCellValue($key, $price . ' EUR');
                }

                if ($fieldMap->hasFieldTotalPrice()) {
                    $key = $this->generateCellKey($cellIndex++, $rowIndex);
                    $price = $this->priceCalculator->calculateTotalForSpecificationItem($item);
                    $activeSheet->setCellValue($key, $price . ' EUR');
                }
            }
        }
        
        $writer = new \PHPExcel_Writer_Excel2007($excel);

        return $writer;
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


    /**
     * Generate characteristic
     *
     * @param ProductVariant $variant
     *
     * @return string
     */
    private function generateVariantCharacteristics(ProductVariant $variant)
    {
        $options = [];

        foreach ($variant->getOptions() as $option) {
            $options[] = sprintf('%s: %s', $option->getName(), $option->getValue());
        }

        foreach ($variant->getSkuOptions() as $option) {
            $options[] = sprintf('%s: %s', $option->getName(), $option->getValue());
        }

        foreach ($variant->getProductPartVariantSelections() as $variantSelection) {
            $options[] = sprintf(
                '%s: %s',
                $variantSelection->getProductPart()->getLabel(),
                $variantSelection->getProductPartMaterialVariant()->getName()
            );
        }

        return implode("\n", $options);
    }

    /**
     * Generate cell key
     *
     * @param int $column
     * @param int $row
     *
     * @return string
     */
    private function generateCellKey($column, $row)
    {
        return sprintf(
            '%s%s',
            $this->generateColumnKey($column),
            $row
        );
    }

    /**
     * Generate a column key
     *
     * @param int $index
     *
     * @return string
     */
    private function generateColumnKey($index)
    {
        $charts = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K'];

        return $charts[$index - 1];
    }

    /**
     * Create a PHP excel instance
     *
     * @param Specification $specification
     *
     * @return \PHPExcel
     */
    private function createPhpExcel(Specification $specification)
    {
        $user = $specification->getCreator();

        $excel = new \PHPExcel();
        $excel->getProperties()->setCreator($user->getUsername());
        $excel->getProperties()->setTitle($specification->getName());
        $excel->getProperties()->setSubject($specification->getName());
        $excel->getProperties()->setDescription($specification->getDescription());

        return $excel;
    }

    /**
     * Get image resource with filter
     *
     * @param string $path
     *
     * @return \Liip\ImagineBundle\Binary\BinaryInterface|null
     */
    private function getImageResourceWithFilter($path, $filter)
    {
        try {
            $binary = $this->imagineDataManager->find($filter, $path);
        } catch (NotLoadableException $e) {
            return null;
        }

        $binary = $this->filterManager->applyFilter($binary, 's100x100');

        return $binary;
    }

    /**
     * Create image for excel
     *
     * @param string $path
     * @param string $coordinate
     *
     * @return \PHPExcel_Worksheet_MemoryDrawing
     */
    private function createImageForExcel($path, $coordinate, $filter = 's100x100')
    {
        $binary = $this->getImageResourceWithFilter($path, $filter);

        $objDrawing = new \PHPExcel_Worksheet_MemoryDrawing();
        
        $imageResource = imagecreatefromstring($binary->getContent());
        
        $objDrawing->setImageResource($imageResource);
        $objDrawing->setCoordinates($coordinate);

        return $objDrawing;
    }
}