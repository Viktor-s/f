<?php

namespace Furniture\SpecificationBundle\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\SpecificationBundle\Entity\Buyer;
use Furniture\SpecificationBundle\Entity\Specification;
use Furniture\SpecificationBundle\Entity\SpecificationItem;
use Furniture\SpecificationBundle\Entity\SpecificationSale;
use Furniture\SpecificationBundle\Form\Type\SpecificationItemSingleType;
use Furniture\SpecificationBundle\Form\Type\SpecificationType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SpecificationController
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
     * Construct
     *
     * @param FormFactoryInterface   $formFactory
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface  $tokenStorage
     */
    public function __construct(
        FormFactoryInterface $formFactory,
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage
    ) {
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Create a new specification
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function create(Request $request)
    {
        $user = $this->tokenStorage->getToken()
            ->getUser();

        $specification = new Specification();
        $specification->setUser($user);

        $form = $this->formFactory->createNamed('', new SpecificationType(), $specification, [
            'csrf_protection' => false
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
            'errors' => $this->convertFormErrorsToArray($form)
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
    public function editItem(Request $request, $item)
    {
        $item = $this->em->find(SpecificationItem::class, $itemId = $item);

        if (!$item) {
            throw new NotFoundHttpException(sprintf(
                'Not found specification item with id "%s".',
                $itemId
            ));
        }

        // @todo: check granted for edit item (via security voter in Symfony)

        $form = $this->formFactory->createNamed('', new SpecificationItemSingleType($this->em), $item, [
            'csrf_protection' => false,
            'method' => 'PATCH'
        ]);

        $form->submit($request, false);

        if ($form->isValid()) {
            $this->em->flush();

            return new JsonResponse([
                'status' => true
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
            case 'note':
                $item->setNote($value);
                break;

            case 'quantity':
                $item->setQuantity($value);
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

    /**
     * Remove item action
     *
     * @param int $item
     *
     * @return JsonResponse
     */
    public function remove($item)
    {
        $item = $this->em->find(SpecificationItem::class, $itemId = $item);

        if (!$item) {
            throw new NotFoundHttpException(sprintf(
                'Not found specification item with identifier "%s".',
                $itemId
            ));
        }

        // @todo: check granted for remove this item

        $this->em->remove($item);
        $this->em->flush();

        return new JsonResponse([
            'status' => true
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
    public function editable(Request $request, $specification)
    {
        $specification = $this->em->find(Specification::class, $specificationId = $specification);

        if (!$specification) {
            throw new NotFoundHttpException(sprintf(
                'Not found specification with identifier "%s".',
                $specificationId
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
                            'Not found buyer with identifier "%s".',
                            $value
                        ));
                    }

                    // @todo: add check granted for use this buyer (via security voter in Symfony)
                    $specification->setBuyer($buyer);

                    $value = (string) $buyer;
                }

                break;

            case 'sale':
                if(!$index = $request->request->get('index')) {
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
                    'Undefined identifier "%s".',
                    $id
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
    public function buyers()
    {
        $user = $this->tokenStorage->getToken()
            ->getUser();

        $buyers = $this->em->createQueryBuilder()
            ->from(Buyer::class, 'b')
            ->select('b.id, b.firstName, b.secondName')
            ->andWhere('b.creator = :creator')
            ->setParameter('creator', $user)
            ->getQuery()
            ->getResult();

        $result = [''];

        foreach ($buyers as $buyerInfo) {
            $result[$buyerInfo['id']] = $buyerInfo['firstName'] . ' ' . $buyerInfo['secondName'];
        }

        return new JsonResponse($result);
    }
}
