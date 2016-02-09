<?php

namespace Furniture\SpecificationBundle\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\SpecificationBundle\Entity\SpecificationItem;
use Furniture\SpecificationBundle\Entity\SkuSpecificationItem;
use Furniture\SpecificationBundle\Form\Type\SkuSpecificationItemSingleType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Furniture\PricingBundle\Calculator\PriceCalculator;
use Furniture\PricingBundle\Twig\PricingExtension;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Furniture\ProductBundle\Entity\ProductPart;
use Furniture\ProductBundle\Entity\ProductPartMaterialVariant;
use Furniture\ProductBundle\Entity\ProductScheme;
use Furniture\ProductBundle\Entity\ProductVariantsPattern;
use Furniture\ProductBundle\Model\ProductPartMaterialVariantSelection;
use Furniture\ProductBundle\Model\ProductPartMaterialVariantSelectionCollection;
use Furniture\ProductBundle\Pattern\Finder\ProductVariantFinder;
use Furniture\ProductBundle\Pattern\ProductVariantCreator;
use Furniture\ProductBundle\Pattern\ProductVariantParameters;


class SkuSpecificationItemController
{

    use FormErrorsTrait;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var PriceCalculator
     */
    private $calculator;

    /**
     *
     * @var PricingExtension
     */
    private $pricingTwigExtension;

    /**
     * @var \Furniture\ProductBundle\Pattern\ProductVariantCreator
     */
    private $productVariantCreator;
    
    /**
     * Construct
     *
     * @param FormFactoryInterface          $formFactory
     * @param EntityManagerInterface        $em
     * @param TokenStorageInterface         $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param PriceCalculator               $calculator
     * @param PricingExtension              $pricingTwigExtension
     * @param ProductVariantCreator         $productVariantCreato
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        PriceCalculator $calculator,
        PricingExtension $pricingTwigExtension,
        ProductVariantCreator $productVariantCreator
    )
    {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->calculator = $calculator;
        $this->pricingTwigExtension = $pricingTwigExtension;
        $this->productVariantCreator = $productVariantCreator;
    }

    
    public function addByPattern(Request $request){
        
        $patternId = $request->request->get('id');
        $choices = $request->request->get('choices');
        
        /* pattern */
        /* @var $pattern \Furniture\ProductBundle\Entity\ProductVariantsPattern */
        $pattern = $this->em->find(ProductVariantsPattern::class, $patternId);
        
        if (!$pattern) {
            throw new NotFoundHttpException(sprintf(
                'Not found product with identifier "%s".',
                $patternId
            ));
        }
        
        /* schemes */
        $scheme = null;
        if( $pattern->getProduct()->isSchematicProductType() ){
            /* @var $scheme \Furniture\ProductBundle\Entity\ProductScheme */
            $scheme = $this->em->find(ProductScheme::class, $choices['ps']);
        }
        
        /* Product parts */
        $productPartMaterialVariantSelectionArray = [];
        foreach( $choices['pp'] as $ppChoice ){
            $productPartMaterialVariantSelectionArray[] = new ProductPartMaterialVariantSelection(
                    $this->em->find( ProductPart::class, $ppChoice['pp']),
                    $this->em->find( ProductPartMaterialVariant::class, $ppChoice['ppv'])
                    );
        }
        $productPartMaterialVariantSelectionCollection = new ProductPartMaterialVariantSelectionCollection($productPartMaterialVariantSelectionArray);
        
        $parameters = new ProductVariantParameters($productPartMaterialVariantSelectionCollection, $scheme);
        
        $variant = $this->productVariantCreator->create($pattern, $parameters);
        
        return new JsonResponse([
            'status' => (bool)$this->addVariantToSpec([
                    'id' => $variant->getId(),
                    'quantity' => $request->request->get('quantity'),
                    'specification' => $request->request->get('specification'),
                ]),
        ]);
    }

    private function addVariantToSpec(array $data) {
        $specificationItem = new SpecificationItem();
        $specificationItem->setSkuItem(new SkuSpecificationItem());

        $form = $this->formFactory->createNamed('', new SkuSpecificationItemSingleType($this->em), $specificationItem, [
            'csrf_protection' => false,
        ]);

        $form->submit($data);

        if ($form->isValid()) {
            $specification = $specificationItem->getSpecification();

            if (!$this->authorizationChecker->isGranted('EDIT', $specification)) {
                throw new AccessDeniedException(sprintf(
                        'The active user "%s" not have rights for edit specification "%s".', $this->tokenStorage->getToken()->getUsername(), $specification->getId()
                ));
            }

            if ($specification->isFinished()) {
                throw new NotFoundHttpException(sprintf(
                        'The specification with identifier "%s" is finished.', $specification->getId()
                ));
            }

            $this->em->persist($specificationItem);
            $this->em->flush();
            return true;
        }
        return false;
    }

    /**
     * Add item to specification
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function add(Request $request)
    {
        $specificationItem = new SpecificationItem();
        $specificationItem->setSkuItem(new SkuSpecificationItem());

        $form = $this->formFactory->createNamed('', new SkuSpecificationItemSingleType($this->em), $specificationItem, [
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $specification = $specificationItem->getSpecification();

            if (!$this->authorizationChecker->isGranted('EDIT', $specification)) {
                throw new AccessDeniedException(sprintf(
                    'The active user "%s" not have rights for edit specification "%s".',
                    $this->tokenStorage->getToken()->getUsername(),
                    $specification->getId()
                ));
            }

            if ($specification->isFinished()) {
                throw new NotFoundHttpException(sprintf(
                    'The specification with identifier "%s" is finished.',
                    $specification->getId()
                ));
            }

            $this->em->persist($specificationItem);
            $this->em->flush();

            return new JsonResponse([
                'status' => true,
            ]);
        }

        return new JsonResponse([
            'status' => false,
            'errors' => $this->convertFormErrorsToArray($form),
        ], 400);
    }

    /**
     * Edit item
     *
     * @param Request $request
     * @param int     $item
     *
     * @return JsonResponse
     */
    public function edit(Request $request, $item)
    {
        $item = $this->em->find(SpecificationItem::class, $itemId = $item);

        if (!$item) {
            throw new NotFoundHttpException(sprintf(
                'Not found specification item with id "%s".',
                $itemId
            ));
        }

        if (!$this->authorizationChecker->isGranted('EDIT', $item)) {
            throw new AccessDeniedException(sprintf(
                'The active user "%s" not have rights for edit specification item.',
                $this->tokenStorage->getToken()->getUsername()
            ));
        }

        $form = $this->formFactory->createNamed('', new SkuSpecificationItemSingleType($this->em), $item, [
            'csrf_protection' => false,
            'method'          => 'PATCH',
        ]);

        $form->submit($request, false);

        if ($form->isValid()) {
            $this->em->flush();

            return new JsonResponse([
                'status' => true,
            ]);
        }

        return new JsonResponse([
            'status' => false,
            'errors' => $this->convertFormErrorsToArray($form),
        ], 400);
    }

}