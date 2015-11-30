<?php

namespace Furniture\SpecificationBundle\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\SpecificationBundle\Entity\Buyer;
use Furniture\SpecificationBundle\Entity\Specification;
use Furniture\SpecificationBundle\Entity\SpecificationItem;
use Furniture\SpecificationBundle\Entity\SpecificationSale;
use Furniture\SpecificationBundle\Form\Type\SkuSpecificationItemSingleType;
use Furniture\SpecificationBundle\Form\Type\SpecificationType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Furniture\PricingBundle\Calculator\PriceCalculator;
use Furniture\PricingBundle\Twig\PricingExtension;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SpecificationController {

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
     * @var PricingExtension
     */
    private $pricingTwigExtension;

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * Construct
     *
     * @param FormFactoryInterface          $formFactory
     * @param EntityManagerInterface        $em
     * @param TokenStorageInterface         $tokenStorage
     * @param PriceCalculator               $calculator
     * @param PricingExtension              $pricingTwigExtension
     * @param AuthorizationCheckerInterface $authorizationChecker
     */
    public function __construct(
    FormFactoryInterface $formFactory, EntityManagerInterface $em, TokenStorageInterface $tokenStorage, PriceCalculator $calculator, PricingExtension $pricingTwigExtension, AuthorizationCheckerInterface $authorizationChecker
    ) {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->calculator = $calculator;
        $this->pricingTwigExtension = $pricingTwigExtension;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Create a new specification
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function create(Request $request) {
        $user = $this->tokenStorage->getToken()
                ->getUser();

        $specification = new Specification();
        $specification->setUser($user);

        $form = $this->formFactory->createNamed('', new SpecificationType(), $specification, [
            'csrf_protection' => false,
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->em->persist($specification);
            $this->em->flush();

            return new JsonResponse([
                'status' => true,
                'id' => $specification->getId(),
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
    public function editItem(Request $request, $item) {
        $item = $this->em->find(SpecificationItem::class, $itemId = $item);

        if (!$item) {
            throw new NotFoundHttpException(sprintf(
                    'Not found specification item with id "%s".', $itemId
            ));
        }

        // @todo: check granted for edit item (via security voter in Symfony)

        $form = $this->formFactory->createNamed('', new SkuSpecificationItemSingleType($this->em), $item, [
            'csrf_protection' => false,
            'method' => 'PATCH',
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

    /**
     * 
     * @param Request $request
     * @param type $specification
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function removeExtraSale(Request $request, $specification) {

        $specification = $this->em->find(Specification::class, $specification);

        if (!$specification) {
            throw new NotFoundHttpException(sprintf(
                    'Not found specification item with identifier "%s".', $itemId
            ));
        }

        $index = $request->request->get('index');
        if ($index === false) {
            throw new NotFoundHttpException('Specification item extrasale index required');
        }

        if ($index > 3) {
            throw new NotFoundHttpException('Invalid "index" parameter.');
        }

        // Fix index for work with collection
        $index = $index - 1;

        $sales = $specification->getSales();

        if ($saleElement = $sales->get($index)) {
            $this->em->remove($saleElement);
        }
        
        $this->em->flush();

        return new JsonResponse([
            'status' => true,
        ]);
    }

    /**
     * Editable item
     *
     * @param Request $request
     * @param int     $item
     *
     * @return Response
     */
    public function editableItem(Request $request, $item) {
        $item = $this->em->find(SpecificationItem::class, $itemId = $item);

        if (!$item) {
            throw new NotFoundHttpException(sprintf(
                    'Not found specification item with identifier "%s".', $itemId
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
            case 'note':
                $item->setNote($value);
                break;

            case 'quantity':
                $item->setQuantity($value);
                break;

            default:
                throw new NotFoundHttpException(sprintf(
                        'Undefined identifier "%s".', $id
                ));
        }

        $this->em->flush();

        return new Response($value);
    }

    /**
     * Remove item action
     *
     * @param int $item
     *
     * @return JsonResponse
     */
    public function remove($item) {
        $item = $this->em->find(SpecificationItem::class, $itemId = $item);

        if (!$item) {
            throw new NotFoundHttpException(sprintf(
                    'Not found specification item with identifier "%s".', $itemId
            ));
        }

        // @todo: check granted for remove this item

        $this->em->remove($item);
        $this->em->flush();

        return new JsonResponse([
            'status' => true,
        ]);
    }

    /**
     * Editable specification
     *
     * @param Request $request
     * @param int     $specification
     *
     * @return Response
     */
    public function editable(Request $request, $specification) {
        $specification = $this->em->find(Specification::class, $specificationId = $specification);

        if (!$specification) {
            throw new NotFoundHttpException(sprintf(
                    'Not found specification with identifier "%s".', $specificationId
            ));
        }

        // @todo: check access granted for edit this specification (via security voter in Symfony)

        $id = $request->request->get('id');
        $value = $request->request->get('value');

        if ($value == 'None') {
            return new Response('None');
        }

        if (!$id) {
            throw new NotFoundHttpException('Missing "id" field.');
        }

        $id = str_replace('specification-', '', $id);

        switch ($id) {
            case 'name':
                $specification->setName($value);
                break;

            case 'description':
                $specification->setDescription($value);
                break;

            case 'buyer':
                if (!$value) {
                    $specification->setBuyer(null);
                    $value = 'None';
                } else {
                    $buyer = $this->em->find(Buyer::class, $value);

                    if (!$buyer) {
                        throw new NotFoundHttpException(sprintf(
                                'Not found buyer with identifier "%s".', $value
                        ));
                    }

                    // @todo: add check granted for use this buyer (via security voter in Symfony)
                    $specification->setBuyer($buyer);

                    $value = (string) $buyer;
                }

                break;

            case 'sale':
                if (!$index = $request->request->get('index')) {
                    throw new NotFoundHttpException('Missing "index" parameter.');
                }

                if ($index > 3) {
                    throw new NotFoundHttpException('Invalid "index" parameter.');
                }

                // Fix index for work with collection
                $index = $index - 1;

                $sales = $specification->getSales();

                if (isset($sales[$index])) {
                    $sales[$index]->setSale($value);
                } else {
                    $sale = new SpecificationSale();
                    $sale
                            ->setSpecification($specification)
                            ->setSale($value);

                    $this->em->persist($sale);
                }

                break;


            default:
                throw new NotFoundHttpException(sprintf(
                        'Undefined identifier "%s".', $id
                ));
        }

        $this->em->flush();

        return new Response($value);
    }

    /**
     * Get available buyers
     *
     * @return JsonResponse
     */
    public function buyers() {
        /** @var \Furniture\CommonBundle\Entity\User $user */
        $user = $this->tokenStorage->getToken()
                ->getUser();
        $retailerProfile = null;
        if ($retailerProfile = $user->getRetailerUserProfile()->getRetailerProfile()) {
            
        }

        $buyers = $this->em->createQueryBuilder()
                ->from(Buyer::class, 'b')
                ->select('b.id, b.firstName, b.secondName')
                ->innerJoin('b.creator', 'rup', 'WITH', 'rup.retailerProfile = :rp')
                ->setParameter('rp', $retailerProfile)
                ->getQuery()
                ->getResult();

        $result = [''];

        foreach ($buyers as $buyerInfo) {
            $result[$buyerInfo['id']] = $buyerInfo['firstName'] . ' ' . $buyerInfo['secondName'];
        }

        return new JsonResponse($result);
    }

    public function info(Request $request, $itemId, $index) {
        /* @var $item \Furniture\SpecificationBundle\Entity\Specification */
        $specification = $this->em->find(Specification::class, $itemId);

        if (!$specification) {
            throw new NotFoundHttpException(sprintf(
                    'Not found specification item with identifier "%s".', $itemId
            ));
        }

        switch ($index) {
            case 'totalPrice':
                return new Response(
                        $this->pricingTwigExtension->money(
                                $this->calculator->calculateForSpecification($specification)
                        )
                );
                break;
        }

        return new Response('');
    }

}
