<?php

namespace Furniture\SpecificationBundle\Exporter;

use Furniture\PricingBundle\Calculator\PriceCalculator;
use Furniture\ProductBundle\Entity\ProductVariant;
use Furniture\SpecificationBundle\Entity\Specification;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Component\Translation\TranslatorInterface;

abstract class AbstractExporter
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

    /**
     * Generate cell key
     *
     * @param int $column
     * @param int $row
     *
     * @return string
     */
    protected function generateCellKey($column, $row)
    {
        return sprintf(
            '%s%s',
            $this->generateColumnKey($column),
            $row
        );
    }

    /**
     * Generate diapason cell key
     *
     * @param int $startColumn
     * @param int $startRow
     * @param int $endColumn
     * @param int $endRow
     *
     * @return string
     */
    protected function generateDiapasonKey($startColumn, $startRow, $endColumn, $endRow)
    {
        return sprintf(
            '%s:%s',
            $this->generateCellKey($startColumn, $startRow),
            $this->generateCellKey($endColumn, $endRow)
        );
    }

    /**
     * Merge
     *
     * @param \PHPExcel_Worksheet $sheet
     * @param int $startColumn
     * @param int $startRow
     * @param int $endColumn
     * @param int $endRow
     *
     * @return string
     */
    protected function mergeDiapason(\PHPExcel_Worksheet $sheet, $startColumn, $startRow, $endColumn, $endRow)
    {
        $key = $this->generateDiapasonKey($startColumn, $startRow, $endColumn, $endRow);
        $sheet->mergeCells($key);
    }

    /**
     * Generate a column key
     *
     * @param int $index
     *
     * @return string
     */
    protected function generateColumnKey($index)
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
    protected function createPhpExcel(Specification $specification)
    {
        $user = $specification->getCreator()->getUser();

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
    protected function getImageResourceWithFilter($path, $filter)
    {
        try {
            $binary = $this->imagineDataManager->find($filter, $path);
        } catch (NotLoadableException$e) {
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
     * @param string $filter
     * @param int    $width
     * @param int    $height
     *
     * @return \PHPExcel_Worksheet_MemoryDrawing
     */
    protected function createImageForExcel($path, $coordinate, $filter = 's100x100', $width = 100, $height = 100)
    {
        $binary = $this->getImageResourceWithFilter($path, $filter);

        $objDrawing = new \PHPExcel_Worksheet_MemoryDrawing();

        $imageResource = imagecreatefromstring($binary->getContent());

        $objDrawing->setImageResource($imageResource);
        $objDrawing->setCoordinates($coordinate);
        $objDrawing->setWidthAndHeight($width, $height);

        return $objDrawing;
    }

    /**
     * Set auto width for column by cell
     *
     * @param \PHPExcel_Cell $cell
     */
    protected function setAutoWidthForColumnByCell(\PHPExcel_Cell $cell)
    {
        $column = $cell->getColumn();
        $sheet = $cell->getWorksheet();

        $sheet->getColumnDimension($column)->setAutoSize(true);
    }

    /**
     * Set alignment for cell
     *
     * @param \PHPExcel_Cell $cell
     * @param string         $horizontal
     * @param string         $vertical
     */
    protected function setAlignmentForCell(
        \PHPExcel_Cell $cell,
        $horizontal = \PHPExcel_Style_Alignment::HORIZONTAL_LEFT,
        $vertical = \PHPExcel_Style_Alignment::VERTICAL_TOP
    )
    {
        $cell->getStyle()->applyFromArray([
            'alignment' => [
                'vertical' => $vertical,
                'horizontal' => $horizontal
            ]
        ]);
    }

    /**
     * Generate characteristic
     *
     * @param ProductVariant $variant
     *
     * @return string
     */
    protected function generateVariantCharacteristics(ProductVariant $variant)
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
     * Format table header
     *
     * @param \PHPExcel_Worksheet $sheet
     * @param string              $diapason
     */
    protected function formatTableHeader(\PHPExcel_Worksheet $sheet, $diapason)
    {
        // Generate style
        $style = [
            'font' => [
                'bold' => true,
            ],

            'alignment' => [
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ],

            'borders' => [
                'allborders' => [
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                    'color' => [
                        'rgb' => '000000',
                    ],
                ],
            ],
        ];

        $headerStyle = $sheet->getStyle($diapason);
        $headerStyle->applyFromArray($style);
    }

    /**
     * Write empty values for cells
     *
     * @param \PHPExcel_Worksheet $sheet
     * @param int $maxColumn
     * @param int $maxRow
     */
    protected function writeEmptyValuesForCells(\PHPExcel_Worksheet $sheet, $maxColumn, $maxRow)
    {
        for ($column = 1; $column <= $maxColumn; $column++) {
            for ($row = 1; $row <= $maxRow; $row++) {
                $key = $this->generateCellKey($column, $row);
                $cell = $sheet->getCell($key);
                if (null === $cell->getValue()) {
                    $cell->setValue('');
                }
            }
        }
    }
}
