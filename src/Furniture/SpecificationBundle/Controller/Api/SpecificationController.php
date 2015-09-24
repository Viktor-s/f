<?php

namespace Furniture\SpecificationBundle\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\SpecificationBundle\Entity\Specification;
use Furniture\SpecificationBundle\Entity\SpecificationItem;
use Furniture\SpecificationBundle\Form\Type\SpecificationItemSingleType;
use Furniture\SpecificationBundle\Form\Type\SpecificationType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
        ]);
    }

    /**
     * Add item to specification
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function addItem(Request $request)
    {
        $specificationItem = new SpecificationItem();

        $form = $this->formFactory->createNamed('', new SpecificationItemSingleType($this->em), $specificationItem, [
            'csrf_protection' => false
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            // @todo: add check granted for add option to this specification
            $this->em->persist($specificationItem);
            $this->em->flush();

            return new JsonResponse([
                'status' => true,
                'id' => $specificationItem->getId()
            ]);
        }

        return new JsonResponse([
            'status' => false,
            'errors' => $this->convertFormErrorsToArray($form)
        ]);
    }
}
