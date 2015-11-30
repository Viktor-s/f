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
     * Construct
     *
     * @param FormFactoryInterface          $formFactory
     * @param EntityManagerInterface        $em
     * @param TokenStorageInterface         $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param PriceCalculator               $calculator
     * @param PricingExtension              $pricingTwigExtension
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        PriceCalculator $calculator,
        PricingExtension $pricingTwigExtension
    )
    {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->calculator = $calculator;
        $this->pricingTwigExtension = $pricingTwigExtension;
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

