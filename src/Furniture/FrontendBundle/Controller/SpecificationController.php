<?php

namespace Furniture\FrontendBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\HttpFoundation\PhpExcelResponse;
use Furniture\FrontendBundle\Repository\Query\SpecificationQuery;
use Furniture\FrontendBundle\Repository\SpecificationRepository;
use Furniture\FrontendBundle\Util\RedirectHelper;
use Furniture\SpecificationBundle\Entity\Specification;
use Furniture\SpecificationBundle\Exporter\ExporterInterface;
use Furniture\SpecificationBundle\Exporter\Client\FieldMapForClient;
use Furniture\SpecificationBundle\Exporter\Factory\FieldMapForFactory;
use Furniture\SpecificationBundle\Form\Type\SpecificationType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

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
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

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
     * @param \Twig_Environment             $twig
     * @param SpecificationRepository       $specificationRepository
     * @param TokenStorageInterface         $tokenStorage
     * @param FormFactoryInterface          $formFactory
     * @param EntityManagerInterface        $em
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param UrlGeneratorInterface         $urlGenerator
     * @param ExporterInterface             $exporter
     */
    public function __construct(
        \Twig_Environment $twig,
        SpecificationRepository $specificationRepository,
        TokenStorageInterface $tokenStorage,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $em,
        AuthorizationCheckerInterface $authorizationChecker,
        UrlGeneratorInterface $urlGenerator,
        ExporterInterface $exporter
    )
    {
        $this->twig = $twig;
        $this->specificationRepository = $specificationRepository;
        $this->tokenStorage = $tokenStorage;
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->authorizationChecker = $authorizationChecker;
        $this->urlGenerator = $urlGenerator;
        $this->exporter = $exporter;
    }

    /**
     * List specifications
     *
     * @param Request $request
     *
     * @return Response
     */
    public function specifications(Request $request)
    {
        if (!$this->authorizationChecker->isGranted('SPECIFICATION_LIST')) {
            throw new AccessDeniedException(sprintf(
                'The user "%s" not have rights for view specifications.',
                $this->tokenStorage->getToken()->getUsername()
            ));
        }

        /** @var \Furniture\UserBundle\Entity\User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $specificationQuery = new SpecificationQuery();
        $sorting = [
            'all'      => [
                'name'  => 'All',
                'state' => false,
            ],
            'opened'   => [
                'name'  => 'Opened',
                'state' => false,
            ],
            'finished' => [
                'name'  => 'Finished',
                'state' => false,
            ],
        ];

        if ($user->getRetailerUserProfile()->isRetailerAdmin()) {
            $retailer = $user->getRetailerUserProfile()->getRetailerProfile();
            $specificationQuery->withRetailer($retailer);
        } else {
            $specificationQuery->withUser($user);
        }

        if ($request->query->has('sorting')) {
            switch ($request->query->get('sorting')) {
                case 'opened':
                    $specificationQuery->opened();
                    $sorting['opened']['state'] = true;
                    break;

                case 'finished':
                    $specificationQuery->finished();
                    $sorting['finished']['state'] = true;
                    break;

                default:
                    $sorting['all']['state'] = true;
            }
        }
        else {
            // By default show only opened specifications
            $specificationQuery->opened();
            $sorting['opened']['state'] = true;
        }

        /* Create product paginator */
        $currentPage = (int)$request->get('page', 1);
        $specifications = $this->specificationRepository->findBy($specificationQuery);

        if ($specifications->getNbPages() < $currentPage) {
            $specifications->setCurrentPage(1);
        } else {
            $specifications->setCurrentPage($currentPage);
        }

        $content = $this->twig->render('FrontendBundle:Specification:specifications.html.twig', [
            'specifications' => $specifications,
            'sorting'        => $sorting,
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

            if (!$this->authorizationChecker->isGranted('EDIT', $specification)) {
                throw new AccessDeniedException(sprintf(
                    'The active user "%s" not have rights for edit specification.',
                    $this->tokenStorage->getToken()->getUsername()
                ));
            }

            if ($specification->isFinished()) {
                throw new NotFoundHttpException(sprintf(
                    'The specification with id "%s" is finished.',
                    $specification->getId()
                ));
            }
        } else {
            $specification = new Specification();
            $specification->setCreator($user->getRetailerUserProfile());
        }

        $form = $this->formFactory->create(new SpecificationType(), $specification, [
            'owner' => $user,
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->em->persist($specification);
            $this->em->flush();

            $url = $this->urlGenerator->generate('specifications');

            return new RedirectResponse($url);
        }

        $content = $this->twig->render('FrontendBundle:Specification:edit.html.twig', [
            'specification'  => $specification,
            'form'           => $form->createView(),
            'active_item_id' => $request->get('item'),
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

        if (!$this->authorizationChecker->isGranted('REMOVE', $specification)) {
            throw new AccessDeniedException(sprintf(
                'The active user "%s" not have rights for remove specification.',
                $this->tokenStorage->getToken()->getUsername()
            ));
        }

        if ($specification->isFinished()) {
            throw new NotFoundHttpException('Can not remove finished specification.');
        }

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

        if (!count($specification->getItems())) {
            throw new NotFoundHttpException(sprintf(
                'Can not finish empty specification with identifier "%s".',
                $specification->getId()
            ));
        }

        if (!$this->authorizationChecker->isGranted('FINISH', $specification)) {
            throw new AccessDeniedException();
        }

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

        if (!count($specification->getItems())) {
            throw new NotFoundHttpException(sprintf(
                'Can not export empty specification with identifier "%s".',
                $specification->getId()
            ));
        }

        if (!$this->authorizationChecker->isGranted('EXPORT', $specification)) {
            throw new AccessDeniedException();
        }
        $groups= [
            'factory' => [],
            'custom' => [],
        ];

        // Group items
        $groupedItemsByFactory = $specification->getGroupedVariantItemsByFactory();
        $groupedCustomItemsByFactory = $specification->getGroupedCustomItemsByFactory();

        foreach ($groupedItemsByFactory as $grouped) {
            $factory = $grouped->getFactory();
            $groups['factory'][$factory->getId()] = $factory->getName();
        }

        foreach ($groupedCustomItemsByFactory as $grouped) {
            $factoryName = $grouped->getFactoryName();
            $groups['custom'][md5($factoryName)] = $factoryName;
        }

        $content = $this->twig->render('FrontendBundle:Specification/Export:preview.html.twig', [
            'specification' => $specification,
            'filters'       => $groups,
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

        if (!count($specification->getItems())) {
            throw new NotFoundHttpException(sprintf(
                'Can not export empty specification with identifier "%s".',
                $specification->getId()
            ));
        }

        if (!$this->authorizationChecker->isGranted('EXPORT', $specification)) {
            throw new AccessDeniedException();
        }

        // Attention: Now we work only with excel exporter
        if ($request->query->get('mode') == 'full') {
            return $this->doExportForClient($specification, $request);

        } else if ($request->query->get('mode') == 'factory') {
            return $this->doExportForFactory($specification, $request);

        } else if ($request->query->get('mode') == 'custom') {
            return $this->doExportForCustom($specification, $request);

        } else {
            throw new NotFoundHttpException(sprintf(
                'Invalid mode "%s".',
                $request->query->get('mode')
            ));
        }
    }

    /**
     * Export for client
     *
     * @param Specification $specification
     * @param Request       $request
     *
     * @return PhpExcelResponse
     */
    private function doExportForClient(Specification $specification, Request $request)
    {
        $fieldMap = new FieldMapForClient($request->query->get('fields', []));
        $format = $request->get('format', 'excel');

        $writer = $this->exporter->exportForClient($specification, $fieldMap, $format);
        $fileName = $this->generateExportFileName($specification->getName(), $format);

        return new PhpExcelResponse($writer, $fileName, $format);
    }

    /**
     * Export for factory
     *
     * @param Specification $specification
     * @param Request       $request
     *
     * @return PhpExcelResponse
     */
    private function doExportForFactory(Specification $specification, Request $request)
    {
        if (!$factoryId = $request->query->get('factory_id')) {
            throw new NotFoundHttpException('Missing "factory_id" parameter.');
        }

        $format = $request->get('format', 'excel');

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
        $writer = $this->exporter->exportForFactory($specification, $fieldMap, $factory, $format);
        $fileName = $this->generateExportFileName($factory->getName(), $format);

        return new PhpExcelResponse($writer, $fileName, $format);
    }

    /**
     * Export for custom factory
     *
     * @param Specification $specification
     * @param Request $request
     *
     * @return PhpExcelResponse
     */
    private function doExportForCustom(Specification $specification, Request $request)
    {
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

        $format = $request->get('format', 'excel');

        $fieldMap = new FieldMapForFactory($request->query->get('fields', []));
        $writer = $this->exporter->exportForCustom($specification, $fieldMap, $groupedItem, $format);
        $name = $groupedItem->getFactoryName();
        $name = $this->generateExportFileName($name, $format);

        return new PhpExcelResponse($writer, $name, $format);
    }

    /**
     * Generate file name with name and format
     *
     * @param string $name
     * @param string $format
     *
     * @return string
     */
    private function generateExportFileName($name, $format)
    {
        if ($format == 'excel') {
            $ext = 'xlsx';
        } else if ($format == 'pdf') {
            $ext = 'pdf';
        } else {
            throw new \InvalidArgumentException(sprintf(
                'Invalid format "%s".',
                $format
            ));
        }

        $replaces = [
            '"' => '',
            "\s" => '-'
        ];
        $name = strtr($name, $replaces);

        return $name . '.' . $ext;
    }
}
