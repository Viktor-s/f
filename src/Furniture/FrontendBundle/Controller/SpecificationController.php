<?php

namespace Furniture\FrontendBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\HttpFoundation\ExcelResponse;
use Furniture\FrontendBundle\Repository\SpecificationRepository;
use Furniture\FrontendBundle\Util\RedirectHelper;
use Furniture\SpecificationBundle\Entity\Specification;
use Furniture\SpecificationBundle\Exporter\ExporterInterface;
use Furniture\SpecificationBundle\Exporter\FieldMapForClient;
use Furniture\SpecificationBundle\Exporter\FieldMapForCustom;
use Furniture\SpecificationBundle\Exporter\FieldMapForFactory;
use Furniture\SpecificationBundle\Form\Type\SpecificationType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SpecificationController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var SpecificationRepository
     */
    private $specificationRepository;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var ExporterInterface
     */
    private $exporter;

    /**
     * Construct
     *
     * @param \Twig_Environment       $twig
     * @param SpecificationRepository $specificationRepository
     * @param TokenStorageInterface   $tokenStorage
     * @param FormFactoryInterface    $formFactory
     * @param EntityManagerInterface  $em
     * @param UrlGeneratorInterface   $urlGenerator
     * @param ExporterInterface       $exporter
     */
    public function __construct(
        \Twig_Environment $twig,
        SpecificationRepository $specificationRepository,
        TokenStorageInterface $tokenStorage,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        ExporterInterface $exporter
    ) {
        $this->twig = $twig;
        $this->specificationRepository = $specificationRepository;
        $this->tokenStorage = $tokenStorage;
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
        $this->exporter = $exporter;
    }

    /**
     * List specifications
     *
     * @return Response
     */
    public function specifications()
    {
        $user = $this->tokenStorage->getToken()->getUser();

        $openedSpecifications = $this->specificationRepository->findOpenedForUser($user);
        $finishedSpecifications = $this->specificationRepository->findFinishedForUser($user);

        $content = $this->twig->render('FrontendBundle:Specification:specifications.html.twig', [
            'opened_specifications' => $openedSpecifications,
            'finished_specifications' =>  $finishedSpecifications
        ]);

        return new Response($content);
    }

    /**
     * Edit/Create specification
     *
     * @param Request $request
     * @param int     $specification
     *
     * @return Response
     */
    public function edit(Request $request, $specification = null)
    {
        $user = $this->tokenStorage->getToken()
            ->getUser();

        if ($specification) {
            $specification = $this->specificationRepository->find($specificationId = $specification);

            if (!$specification) {
                throw new NotFoundHttpException(sprintf(
                    'Not found specification with identifier "%s".',
                    $specificationId
                ));
            }

            // @todo: check grant for edit item (via security voter in Symfony)
        } else {
            $specification = new Specification();
            $specification->setUser($user);
        }

        $form = $this->formFactory->create(new SpecificationType(), $specification, [
            'owner' => $user
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->em->persist($specification);
            $this->em->flush();

            $url = $this->urlGenerator->generate('specifications');

            return new RedirectResponse($url);
        }

        $content = $this->twig->render('FrontendBundle:Specification:edit.html.twig', [
            'specification' => $specification,
            'form' => $form->createView(),
            'active_item_id' => $request->get('item')
        ]);

        return new Response($content);
    }

    /**
     * Remove specification
     *
     * @param Request $request
     * @param int     $specification
     *
     * @return RedirectResponse
     */
    public function remove(Request $request, $specification)
    {
        $specification = $this->specificationRepository->find($specificationId = $specification);

        if (!$specification) {
            throw new NotFoundHttpException(sprintf(
                'Not found specification with identifier "%s".',
                $specificationId
            ));
        }

        // @todo: add check granted for remove item (via security voter in symfony)

        $this->em->remove($specification);
        $this->em->flush();

        $url = $this->urlGenerator->generate('specifications');
        $url = RedirectHelper::getRedirectUrl($request, $url);

        return new RedirectResponse($url);
    }

    /**
     * Finish specification
     *
     * @param Request $request
     * @param int     $specification
     *
     * @return RedirectResponse
     */
    public function finish(Request $request, $specification)
    {
        $specification = $this->specificationRepository->find($specificationId = $specification);

        if (!$specification) {
            throw new NotFoundHttpException(sprintf(
                'Not found specification with identifier "%s".',
                $specificationId
            ));
        }

        // @todo: add check granted for finish specification (via security voter in Symfony)

        $specification->finish();

        $this->em->flush();

        $url = $this->urlGenerator->generate('specifications');
        $url = RedirectHelper::getRedirectUrl($request, $url);

        return new RedirectResponse($url);
    }

    /**
     * Export preview action
     *
     * @param int $specification
     *
     * @return Response
     */
    public function exportPreview($specification)
    {
        $specification = $this->specificationRepository->find($specificationId = $specification);

        if (!$specification) {
            throw new NotFoundHttpException(sprintf(
                'Not found specification with identifier "%s".',
                $specificationId
            ));
        }

        // @todo: add check granted for export this specification (via security voter in Symfony)

        // Group items
        $groupedItemsByFactory = $specification->getGroupedVariantItemsByFactory();
        $groupedCustomItemsByFactory = $specification->getGroupedCustomItemsByFactory();

        $content = $this->twig->render('FrontendBundle:Specification/Export:preview.html.twig', [
            'specification' => $specification,
            'grouped_items_by_factory' => $groupedItemsByFactory,
            'grouped_custom_items_by_factory' => $groupedCustomItemsByFactory
        ]);

        return new Response($content);
    }

    /**
     * Export specification
     *
     * @param Request $request
     * @param int     $specification
     *
     * @return Response
     */
    public function export(Request $request, $specification)
    {
        $specification = $this->specificationRepository->find($specificationId = $specification);

        if (!$specification) {
            throw new NotFoundHttpException(sprintf(
                'Not found specification with identifier "%s".',
                $specificationId
            ));
        }

        // @todo: add check granted for export this specification (via security voter in Symfony)

        // Attention: Now we work only with excel exporter
        if ($request->query->get('mode') == 'full') {
            $fieldMap = new FieldMapForClient($request->query->get('fields', []));
            $writer = $this->exporter->exportForClient($specification, $fieldMap);

            return new ExcelResponse($writer, 'specification.xlsx');
        } else if ($request->query->get('mode') == 'factory') {
            if (!$factoryId = $request->query->get('factory_id')) {
                throw new NotFoundHttpException('Missing "factory_id" parameter.');
            }

            // Search factory
            $factory = null;
            foreach ($specification->getItems() as $item) {
                if (!$item->getSkuItem()) {
                    continue;
                }

                /** @var \Furniture\ProductBundle\Entity\Product $product */
                $product = $item->getSkuItem()->getProductVariant()->getProduct();
                $itemFactory = $product->getFactory();

                if ($itemFactory->getId() == $factoryId) {
                    $factory = $itemFactory;
                    break;
                }
            }

            if (!$factory) {
                throw new NotFoundHttpException(sprintf(
                    'Not found factory with identifier "%s" for specification "%s [%d]".',
                    $factoryId,
                    $specification->getName(),
                    $specification->getId()
                ));
            }

            $fieldMap = new FieldMapForFactory($request->query->get('fields', []));
            $writer = $this->exporter->exportForFactory($specification, $fieldMap, $factory);

            return new ExcelResponse($writer, $factory->getName() . '.xlsx');
        } else if ($request->query->get('mode') == 'custom') {
            if (!$factoryName = $request->query->get('factory_name')) {
                throw new NotFoundHttpException('Missing "factory_name" parameter.');
            }

            $groupedCustomItems = $specification->getGroupedCustomItemsByFactory();

            // Search by factory name
            $groupedItem = null;
            foreach ($groupedCustomItems as $gItem) {
                if (strtolower($gItem->getFactoryName()) == strtolower($factoryName)) {
                    $groupedItem = $gItem;
                    break;
                }
            }

            if (!$groupedItem) {
                throw new NotFoundHttpException(sprintf(
                    'Not found factory with name "%s" for specification "%s [%d]".',
                    $factoryName,
                    $specification->getName(),
                    $specification->getId()
                ));
            }

            $fieldMap = new FieldMapForCustom($request->query->get('fields', []));
            $writer = $this->exporter->exportForCustom($specification, $fieldMap, $groupedItem);

            return new ExcelResponse($writer, $groupedItem->getFactoryName() . '.xlsx');

        } else {
            throw new NotFoundHttpException(sprintf(
                'Invalid mode "%s".',
                $request->query->get('mode')
            ));
        }
    }
}
