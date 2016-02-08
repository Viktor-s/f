<?php

namespace Furniture\ProductBundle\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\PricingBundle\Calculator\PriceCalculator;
use Furniture\ProductBundle\Entity\ProductPart;
use Furniture\ProductBundle\Entity\ProductPartMaterialVariant;
use Furniture\ProductBundle\Entity\ProductScheme;
use Furniture\ProductBundle\Entity\ProductVariantsPattern;
use Furniture\ProductBundle\Model\ProductPartMaterialVariantSelection;
use Furniture\ProductBundle\Model\ProductPartMaterialVariantSelectionCollection;
use Furniture\ProductBundle\Pattern\ProductVariantCreator;
use Furniture\ProductBundle\Pattern\ProductVariantParameters;
use Furniture\ProductBundle\Pattern\ProductVariantParametersValidator;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PatternProductCreatorController
{
    /**
     * @var ProductVariantCreator
     */
    private $productVariantCreator;

    /**
     * @var ProductVariantParametersValidator
     */
    private $parametersValidator;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var PriceCalculator
     */
    private $priceCalculator;

    /**
     * Construct
     *
     * @param ProductVariantCreator             $creator
     * @param ProductVariantParametersValidator $parametersValidator
     * @param FormFactoryInterface              $formFactory
     * @param EntityManagerInterface            $em
     * @param PriceCalculator                   $priceCalculator
     */
    public function __construct(
        ProductVariantCreator $creator,
        ProductVariantParametersValidator $parametersValidator,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $em,
        PriceCalculator $priceCalculator
    )
    {
        $this->productVariantCreator = $creator;
        $this->parametersValidator = $parametersValidator;
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->priceCalculator = $priceCalculator;
    }

    /**
     * Create a product variant by product parts, sku options and another input parameters
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function createProductVariant(Request $request)
    {
        $data = $request->request;

        $patternId = $data->getInt('id');

        if (!$patternId) {
            throw new HttpException(400, 'Missing "id" in request or send non integer data.');
        }

        $choices = $data->get('choices');

        if (!$choices || !is_array($choices)) {
            throw new HttpException(400, 'Missing "choices" in request or send non array data.');
        }

        // Load pattern
        $pattern = $this->em->find(ProductVariantsPattern::class, $patternId);

        if (!$pattern) {
            throw new NotFoundHttpException(sprintf(
                'Not found pattern with identifier "%s".',
                $patternId
            ));
        }

        if (!isset($choices['pp'])) {
            throw new HttpException(400, 'Missing "pp" in choices.');
        }

        // Load scheme
        $scheme = null;

        // We not check by "has", because we can send empty data
        if ($data->get('ps')) {
            $schemeId = $data->getInt('ps');

            if (!$schemeId) {
                throw new HttpException(400, 'Invalid "ps" parameter.');
            }

            $scheme = $this->em->find(ProductScheme::class, $schemeId);

            if (!$scheme) {
                throw new NotFoundHttpException(sprintf(
                    'Not found product scheme with identifier "%s".',
                    $schemeId
                ));
            }
        }

        // Load choices
        $productPartVariantSelections = [];

        foreach ($choices['pp'] as $index => $choiceData) {
            if (!is_array($choiceData)) {
                throw new HttpException(400, sprintf(
                    'Invalid choice with index "%s". Should be a array.',
                    $index
                ));
            }

            if (!isset($choiceData['pp']) || !isset($choiceData['ppv'])) {
                throw new HttpException(400, sprintf(
                    'Missing requires data "pp" or "ppv" in choice data with index "%s".',
                    $index
                ));
            }

            $productPartId = (int) $choiceData['pp'];
            $productPartVariantId = (int) $choiceData['ppv'];

            if (!$productPartId || !$productPartVariantId) {
                throw new HttpException(400, sprintf(
                    'Invalid "pp" or "ppv" in choice data with index "%s".',
                    $index
                ));
            }

            $productPart = $this->em->find(ProductPart::class, $productPartId);

            if (!$productPart) {
                throw new NotFoundHttpException(sprintf(
                    'Not found product part with identifier "%s" in choice data with index "%s".',
                    $productPartId,
                    $index
                ));
            }

            $productPartVariant = $this->em->find(ProductPartMaterialVariant::class, $productPartVariantId);

            if (!$productPartVariant) {
                throw new NotFoundHttpException(sprintf(
                    'Not found product part variant with identifier "%s" in choice data with index "%s".',
                    $productPartVariantId,
                    $index
                ));
            }

            $productPartVariantSelections[] = new ProductPartMaterialVariantSelection($productPart, $productPartVariant);
        }

        // Convert selections from array to collection object
        $productPartVariantSelections = new ProductPartMaterialVariantSelectionCollection($productPartVariantSelections);

        // Create parameters for next validate and create variant
        $productVariantParameters = new ProductVariantParameters($productPartVariantSelections, $scheme);

        // Validate
        $violations = $this->parametersValidator->validateByPattern($pattern, $productVariantParameters);

        if (count($violations)) {
            // @todo: control validation error
        }

        $variant = $this->productVariantCreator->create($pattern, $productVariantParameters);

        return new JsonResponse([
            'id' => $variant->getId(),
            'price' => $this->priceCalculator->calculateForProductVariant($variant)
        ]);
    }
}


//$ref = new \ReflectionObject($this->productRepository);
//$ref = $ref->getProperty('em');
//$ref->setAccessible(true);
//$em = $ref->getValue($this->productRepository);
//$part = $em->find('Furniture\ProductBundle\Entity\ProductPart', 22);
//$pattern = $em->find('Furniture\ProductBundle\Entity\ProductVariantsPattern', 3);
//$scheme = $em->find('Furniture\ProductBundle\Entity\ProductScheme', 1);
//
//$partMaterialVariant11 = $em->find('Furniture\ProductBundle\Entity\ProductPartMaterialVariant', 14);
//$partMaterialVariant12 = $em->find('Furniture\ProductBundle\Entity\ProductPartMaterialVariant', 15);
//
//$partMaterialVariant21 = $em->find('Furniture\ProductBundle\Entity\ProductPartMaterialVariant', 32);
//$partMaterialVariant22 = $em->find('Furniture\ProductBundle\Entity\ProductPartMaterialVariant', 31);
//
//$selection1 = new ProductPartMaterialVariantSelection($part, $partMaterialVariant11);
//$selection2 = new ProductPartMaterialVariantSelection($part, $partMaterialVariant12);
//$selection3 = new ProductPartMaterialVariantSelection($part, $partMaterialVariant21);
//$selection4 = new ProductPartMaterialVariantSelection($part, $partMaterialVariant22);
//$selections = new ProductPartMaterialVariantSelectionCollection([$selection1, $selection2, $selection3, $selection4]);
//
//$parameters = new ProductVariantParameters($selections, $scheme);
//$variant = $this->productVariantCreator->create($pattern, $parameters);
//var_dump($variant);exit();