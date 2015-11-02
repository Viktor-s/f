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

    /**
     * {@inheritDoc}
     */
    public function exportForClient(Specification $specification, FieldMapForClient $fieldMap)
    {
        $excel = $this->createPhpExcel($specification);
        $excel->setActiveSheetIndex(0);
        $activeSheet = $excel->getActiveSheet();

        // Create headers
        $index = 1;
        if ($fieldMap->hasFieldNumber()) {
            $key = $this->generateCellKey($index++, 4);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.number'));
        }

        if ($fieldMap->hasFieldFactory()) {
            $key = $this->generateCellKey($index++, 4);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.factory'));
        }

        if ($fieldMap->hasFieldPhoto()) {
            $key = $this->generateCellKey($index++, 4);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.photo'));
        }

        if ($fieldMap->hasFieldName()) {
            $key = $this->generateCellKey($index++, 4);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.name'));
        }

        if ($fieldMap->hasFieldArticle()) {
            $key = $this->generateCellKey($index++, 4);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.article'));
        }

        if ($fieldMap->hasFieldSize()) {
            $key = $this->generateCellKey($index++, 4);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.size'));
        }

        if ($fieldMap->hasFieldFinishes()) {
            $key = $this->generateCellKey($index++, 4);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.finishes'));
        }

        if ($fieldMap->hasFieldCharacteristics()) {
            $key = $this->generateCellKey($index++, 4);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.characteristics'));
        }

        if ($fieldMap->hasFieldQuantity()) {
            $key = $this->generateCellKey($index++, 4);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.quantity'));
        }

        if ($fieldMap->hasFieldPrice()) {
            $key = $this->generateCellKey($index++, 4);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.price'));
        }

        if ($fieldMap->hasFieldTotalPrice()) {
            $key = $this->generateCellKey($index, 4);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.total_price'));
        }

        $countColumns = $index;

        $rowIndex = 5;
        $numberOfRecords = 1;
        foreach ($specification->getItems() as $item) {
            $cellIndex = 1;

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

            $rowIndex++;
        }

        // Add header
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
        if ($fieldMap->hasFieldNumber()) {
            $key = $this->generateCellKey($index++, 6);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.number'));
        }

        if ($fieldMap->hasFieldPhoto()) {
            $key = $this->generateCellKey($index++, 6);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.photo'));
        }

        if ($fieldMap->hasFieldSku()) {
            $key = $this->generateCellKey($index++, 6);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.sku'));
        }

        if ($fieldMap->hasFieldNote()) {
            $key = $this->generateCellKey($index++, 6);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.description'));
        }

        if ($fieldMap->hasFieldSize()) {
            $key = $this->generateCellKey($index++, 6);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.size'));
        }

        if ($fieldMap->hasFieldQuantity()) {
            $key = $this->generateCellKey($index++, 6);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.quantity'));
        }

        $countColumns = $index;
        $rowIndex = 7;
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

                $image = $item->getSkuItem()->getProductVariant()->getImage();

                if ($image && $image->getPath()) {
                    $obj = $this->createImageForExcel($image->getPath(), $key);
                    $obj->setWorksheet($activeSheet);
                }
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

        // Add header
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
            $factory->getName()
        ));

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
        if ($fieldMap->hasFieldNumber()) {
            $key = $this->generateCellKey($index++, 6);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.number'));
        }

        if ($fieldMap->hasFieldPhoto()) {
            $key = $this->generateCellKey($index++, 6);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.photo'));
        }

        if ($fieldMap->hasFieldName()) {
            $key = $this->generateCellKey($index++, 6);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.name'));
        }

        if ($fieldMap->hasFieldQuantity()) {
            $key = $this->generateCellKey($index++, 6);
            $activeSheet->setCellValue($key, $this->translator->trans('specification.excel.quantity'));
        }

        $countColumns = $index;
        $rowIndex = 7;
        $numberOfRecords = 1;

        foreach ($grouped->getItems() as $item) {
            $cellIndex = 1;

            if ($fieldMap->hasFieldNumber()) {
                $key = $this->generateCellKey($cellIndex++, $rowIndex);
                $activeSheet->setCellValue($key, $numberOfRecords++);
            }

            if ($fieldMap->hasFieldPhoto()) {
                $key = $this->generateCellKey($cellIndex++, $rowIndex);

                if ($item->getCustomItem()->getImage() && $item->getCustomItem()->getImage()->getPath()) {
                    $obj = $this->createImageForExcel($item->getCustomItem()->getImage()->getPath(), $key);
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
        ));

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
        $user = $specification->getUser();

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
    private function getImageResourceWithFilter($path)
    {
        try {
            $binary = $this->imagineDataManager->find('s100x100', $path);
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
    private function createImageForExcel($path, $coordinate)
    {
        $binary = $this->getImageResourceWithFilter($path);

        $objDrawing = new \PHPExcel_Worksheet_MemoryDrawing();
        $objDrawing->setHeight(100);
        $objDrawing->setWidth(100);

        $imageResource = imagecreatefromstring($binary->getContent());
        $objDrawing->setImageResource($imageResource);
        $objDrawing->setCoordinates($coordinate);

        return $objDrawing;
    }
}