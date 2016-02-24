<?php

namespace Furniture\SpecificationBundle\Exporter;

use Furniture\PricingBundle\Calculator\PriceCalculator;
use Furniture\ProductBundle\Entity\ProductVariant;
use Furniture\SpecificationBundle\Entity\Specification;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use PHPExcel_Shared_Drawing;
use PHPExcel_Style_Font;
use Symfony\Component\Translation\TranslatorInterface;

abstract class AbstractExporter
{
    /**
     * Row number from which sheet header starts.
     */
    const SHEET_HEADER_START_ROW = 1;

    /**
     * Row number from which sheet data table starts.
     */
    const SHEET_DATA_START_ROW = 14;

    /**
     * Index of first header column.
     */
    const SHEET_HEADER_FIRST_COL_INDEX = 3;

    /**
     * Index of second header column.
     */
    const SHEET_HEADER_SECOND_COL_INDEX = 5;

    /**
     * Number of columns that sheet top header takes;
     */
    const SHEET_HEADER_LENGTH = 8;

    /**
     * Constant to determine imgae row height.
     * Measurement in pt.
     */
    const IMAGE_ROW_HEIGHT = 90;

    /**
     * Constant to determine imgae column width.
     * Quantity of characters that fits into the cell. (Default font Calibri 11)
     */
    const IMAGE_COLUMN_WIDTH = 15;

    /**
     * Logo image width in px.
     */
    const LOGO_IMAGE_WIDTH = 150;

    /**
     * Logo image height in px.
     */
    const LOGO_IMAGE_HEIGHT = 100;

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
     * @var PHPExcel_Style_Font
     */
    protected $defaultFont;

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
     * @param int                 $startColumn
     * @param int                 $startRow
     * @param int                 $endColumn
     * @param int                 $endRow
     *
     * @return \PHPExcel_Cell
     */
    protected function mergeDiapason(\PHPExcel_Worksheet $sheet, $startColumn, $startRow, $endColumn, $endRow)
    {
        $key = $this->generateDiapasonKey($startColumn, $startRow, $endColumn, $endRow);
        $sheet->mergeCells($key);

        return $sheet->getCell($this->generateCellKey($startColumn, $startRow));
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

        $this->setDefaultFont($excel->getDefaultStyle()->getFont());

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
     * @param int    $cellWidth
     * @param int    $rowHeight
     * @return \PHPExcel_Worksheet_MemoryDrawing
     */
    protected function createImageForExcel(
        $path,
        $coordinate,
        $filter = 's100x100',
        $width = 100,
        $height = 100,
        $cellWidth = self::IMAGE_COLUMN_WIDTH,
        $rowHeight = self::IMAGE_ROW_HEIGHT
    )
    {
        $binary = $this->getImageResourceWithFilter($path, $filter);

        $objDrawing = new \PHPExcel_Worksheet_MemoryDrawing();

        $imageResource = imagecreatefromstring($binary->getContent());

        $objDrawing->setImageResource($imageResource);
        $objDrawing->setCoordinates($coordinate);
        $objDrawing->setWidthAndHeight($width, $height);

        if (!$this->defaultFont) {
            $this->setDefaultFont();
        }
        $rowPxHeight = PHPExcel_Shared_Drawing::pointsToPixels($rowHeight);
        $columnPxWidth = PHPExcel_Shared_Drawing::cellDimensionToPixels($cellWidth, $this->defaultFont);

        $offsetX = ceil(($columnPxWidth - $objDrawing->getWidth()) / 2);
        $offsetY = ceil(($rowPxHeight - $objDrawing->getHeight()) / 2);

        $objDrawing->setOffsetX($offsetX);
        $objDrawing->setOffsetY($offsetY);

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
        $cell->getStyle()->applyFromArray(
            [
                'alignment' => [
                    'vertical'   => $vertical,
                    'horizontal' => $horizontal,
                ],
            ]
        );
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
            'font'      => [
                'bold' => true,
            ],
            'alignment' => [
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ],
            'borders'   => [
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
     * Function to delete formating at the end of the rows.
     *
     * @param \PHPExcel_Worksheet $sheet
     * @param int                 $lastColumn
     * @param int                 $maxRow
     * @throws \PHPExcel_Exception
     */
    public function formatTable(\PHPExcel_Worksheet $sheet, $lastColumn, $maxRow)
    {
        $headeStartRow = self::SHEET_HEADER_START_ROW;
        $headeLength = self::SHEET_HEADER_LENGTH;
        if ($lastColumn < $headeLength) {
            $key = $this->generateDiapasonKey($lastColumn + 1, $headeStartRow, $headeLength, $maxRow);
            $rowStyle = $sheet->getStyle($key);
            $rowStyle->applyFromArray(
                [
                    'borders' => [
                        'allborders' => [
                            'style' => \PHPExcel_Style_Border::BORDER_NONE,
                        ],
                    ],
                ]
            );
        }

        $this->adjustLogoImageCell($sheet);
    }

    /**
     * Adjust logo image cells width.
     *
     * @param \PHPExcel_Worksheet $sheet
     */
    private function adjustLogoImageCell(\PHPExcel_Worksheet $sheet)
    {
        $sheet->calculateColumnWidths();

        $key = $this->generateColumnKey(self::SHEET_HEADER_FIRST_COL_INDEX + 1);
        $key2 = $this->generateColumnKey(self::SHEET_HEADER_FIRST_COL_INDEX + 2);

        $logoCellWidth = ceil($sheet->getColumnDimension($key)->getWidth());
        $logoCellWidth2 = ceil($sheet->getColumnDimension($key2)->getWidth());

        $logoColumnsWidth = $logoCellWidth + $logoCellWidth2;
        $imageWidth = ceil(PHPExcel_Shared_Drawing::pixelsToCellDimension(self::LOGO_IMAGE_WIDTH, $this->defaultFont));

        if ($imageWidth > $logoColumnsWidth) {
            $increase = round(($imageWidth - $logoColumnsWidth) / 2);
            $sheet->getColumnDimension($key)->setAutoSize(false)->setWidth($logoCellWidth + $increase);
            $sheet->getColumnDimension($key2)->setAutoSize(false)->setWidth($logoCellWidth2 + $increase);
        }
    }

    /**
     * Write empty values for cells
     *
     * @param \PHPExcel_Worksheet $sheet
     * @param int                 $maxColumn
     * @param int                 $maxRow
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

    /**
     * Set default font for process.
     *
     * @param PHPExcel_Style_Font|null $font
     */
    private function setDefaultFont(PHPExcel_Style_Font $font = null)
    {
        if ($font) {
            $this->defaultFont = $font;
        } else {
            $this->defaultFont = new PHPExcel_Style_Font();
        }
    }
}
