<?php

namespace Furniture\SpecificationBundle\Exporter;

use Furniture\FactoryBundle\Entity\Factory;
use Furniture\PricingBundle\Calculator\PriceCalculator;
use Furniture\ProductBundle\Entity\ProductVariant;
use Furniture\SpecificationBundle\Entity\Specification;
use Furniture\SpecificationBundle\Exporter\Client\ClientExporter;
use Furniture\SpecificationBundle\Exporter\Client\FieldMapForClient;
use Furniture\SpecificationBundle\Exporter\Factory\CustomFactoryExporter;
use Furniture\SpecificationBundle\Exporter\Factory\FactoryExporter;
use Furniture\SpecificationBundle\Exporter\Factory\FieldMapForFactory;
use Furniture\SpecificationBundle\Model\GroupedCustomItemsByFactory;
use Liip\ImagineBundle\Exception\Binary\Loader\NotLoadableException;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Component\Translation\TranslatorInterface;
use Sylius\Bundle\CurrencyBundle\Templating\Helper\MoneyHelper;
use Sylius\Component\Currency\Context\CurrencyContextInterface;

class PhpExcelExporter implements ExporterInterface
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
    public function exportForClient(Specification $specification, FieldMapForClient $fieldMap, $format)
    {
        $internalExporter = new ClientExporter(
            $this->translator,
            $this->priceCalculator,
            $this->imagineDataManager,
            $this->filterManager
        );

        $excel = $internalExporter->export($specification, $fieldMap);

        return $this->createWriter($excel, $format);
    }

    /**
     * {@inheritDoc}
     */
    public function exportForFactory(Specification $specification, FieldMapForFactory $fieldMap, Factory $factory, $format)
    {
        $internalExporter = new FactoryExporter(
            $this->translator,
            $this->priceCalculator,
            $this->imagineDataManager,
            $this->filterManager
        );

        $excel = $internalExporter->export($specification, $fieldMap, $factory);

        return $this->createWriter($excel, $format);
    }

    /**
     * {@inheritDoc}
     */
    public function exportForCustom(
        Specification $specification,
        FieldMapForFactory $fieldMap,
        GroupedCustomItemsByFactory $grouped,
        $format
    )
    {
        $internalExporter = new CustomFactoryExporter(
            $this->translator,
            $this->priceCalculator,
            $this->imagineDataManager,
            $this->filterManager
        );

        $excel = $internalExporter->export($specification, $fieldMap, $grouped);

        return $this->createWriter($excel, $format);
    }

    /**
     * Create writer via format
     *
     * @param \PHPExcel $excel
     * @param string    $format
     *
     * @return \PHPExcel_Writer_IWriter
     */
    private function createWriter(\PHPExcel $excel, $format)
    {
        if ($format == 'excel') {
            return new \PHPExcel_Writer_Excel2007($excel);
        }

        if ($format == 'pdf') {
            $vendorDir = realpath(__DIR__ . '/../../../../vendor');
            \PHPExcel_Settings::setPdfRendererPath($vendorDir . '/mpdf/mpdf');

            return new \PHPExcel_Writer_PDF_mPDF($excel);
        }

        throw new \InvalidArgumentException(sprintf(
            'Invalid format "%s". Available formats: "excel" or "pdf".',
            $format
        ));
    }
}
