<?php

namespace Furniture\SpecificationBundle\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\SpecificationBundle\Entity\SpecificationItem;
use Furniture\SpecificationBundle\Entity\CustomSpecificationItem;
use Furniture\SpecificationBundle\Form\Type\CustomSpecificationItemSingleType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Furniture\PricingBundle\Calculator\PriceCalculator;
use Furniture\PricingBundle\Twig\PricingExtension;

class CustomSpecificationItemController 
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
     * @param FormFactoryInterface   $formFactory
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface  $tokenStorage
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        PriceCalculator $calculator,
        PricingExtension $pricingTwigExtension
    ) {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
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
        $specificationItem->setCustomItem(new CustomSpecificationItem());

        $form = $this->formFactory->createNamed('', new CustomSpecificationItemSingleType($this->em), $specificationItem, [
            'csrf_protection' => false
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            // @todo: add check granted for add option to this specification
            $this->em->persist($specificationItem);
            $this->em->flush();

            return new JsonResponse([
                'status' => true,
                'data' => [
                    'id' => $specificationItem->getId(),
                    'customId' => $specificationItem->getCustomItem()->getId(),
                    'specification' => $specificationItem->getSpecification()->getId(),
                    'factoryName' => $specificationItem->getCustomItem()->getFactoryName(),
                    'name' => $specificationItem->getCustomItem()->getName(),
                    'price' => $specificationItem->getCustomItem()->getPrice(),
                    'quantity' => $specificationItem->getQuantity(),
                    'note' => $specificationItem->getNote()
                ]
            ]);
        }

        return new JsonResponse([
            'status' => false,
            'errors' => $this->convertFormErrorsToArray($form)
        ], 400);
    }
    
    /**
     * Editable item
     *
     * @param Request $request
     * @param int     $item
     *
     * @return Response
     */
    public function editableItem(Request $request, $item)
    {
        /* @var $item \Furniture\SpecificationBundle\Entity\SpecificationItem */
        $item = $this->em->find(SpecificationItem::class, $itemId = $item);

        if (!$item) {
            throw new NotFoundHttpException(sprintf(
                'Not found specification item with identifier "%s".',
                $itemId
            ));
        }

        // @todo: add check granted for edit this item (via security voter in symfony)

        $id = $request->request->get('id');
        $value = $request->request->get('value');

        if ($value == 'None') {
            return new Response('None');
        }

        if (!$id) {
            throw new NotFoundHttpException('Missing "id" field.');
        }

        $id = str_replace('specification-item-', '', $id);

        switch ($id) {
            case 'factoryName':
                $item->getCustomItem()->setFactoryName($value);
                break;

            case 'name':
                $item->getCustomItem()->setName($value);
                break;
            case 'price':
                 $item->getCustomItem()->setPrice($value*100);
                break;
            default:
                throw new NotFoundHttpException(sprintf(
                    'Undefined identifier "%s".',
                    $id
                ));
        }

        $this->em->flush();

        return new Response($value);
    }
    
    public function info(Request $request, $itemId, $index)
    {
        /* @var $item \Furniture\SpecificationBundle\Entity\SpecificationItem */
        $specificationItem = $this->em->find(SpecificationItem::class, $itemId);
        
        if (!$specificationItem) {
            throw new NotFoundHttpException(sprintf(
                'Not found specification item with identifier "%s".',
                $itemId
            ));
        }
        
        switch($index){
            case 'totalPrice':
                return new Response( 
                            $this->pricingTwigExtension->money(
                                $this->calculator->calculateForSpecificationItem($specificationItem)
                            )
                        );
                break;
        }
        return new Response('');
    }
    
}

