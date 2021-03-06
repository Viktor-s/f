<?php

namespace Furniture\FrontendBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Furniture\CommonBundle\HttpFoundation\PhpExcelResponse;
use Furniture\CommonBundle\Util\SimpleChoiceList;
use Furniture\FrontendBundle\Repository\Query\SpecificationQuery;
use Furniture\FrontendBundle\Repository\SpecificationRepository;
use Furniture\FrontendBundle\Util\RedirectHelper;
use Furniture\RetailerBundle\Entity\RetailerUserProfile;
use Furniture\SpecificationBundle\Entity\Buyer;
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
        /** @var RetailerUserProfile $retailerProfile */
        $retailerUserProfile = $user->getRetailerUserProfile();
        $retailer = $retailerUserProfile->getRetailerProfile();
        $specificationQuery = new SpecificationQuery();
        $filters = new SimpleChoiceList();
        $sortingUser = null;

        if ($retailerUserProfile->isRetailerAdmin()) {
            $filters->addChoice('opened', 'Opened', ['group' => 'all']);
            $filters->addChoice('finished', 'Finished', ['group' => 'all']);
            $filters->addChoice('opened__'.$user->getId(), 'Opened', ['group' => 'my']);
            $filters->addChoice('finished__'.$user->getId(), 'Finished', ['group' => 'my']);

            // Disable softdeleteable filter, because retailer user profile can contain deleted users.
            $this->em->getFilters()->disable('softdeleteable');
            /** @var RetailerUserProfile $retailerUserProfile */
            foreach ($retailer->getRetailerUserProfiles() as $retailUserProfile) {
                if (!$retailUserProfile->getUser()->isDeleted()) {
                    if ($retailUserProfile->getId() !== $retailerUserProfile->getId()) {
                        $retailerUser = $retailUserProfile->getUser();
                        $fullName = str_replace(' ', '_',
                                                sprintf(
                                                    '%s (%s)',
                                                    strtolower($retailerUser->getFullName()),
                                                    $retailerUser->getEmail()
                                                )
                        );
                        $filters->addChoice('opened__'.$retailerUser->getId(), 'Opened', ['group' => $fullName]);
                        $filters->addChoice('finished__'.$retailerUser->getId(), 'Finished', ['group' => $fullName]);
                    }
                }

                if ($request->query->has('filter_user')
                    && $retailUserProfile->getUser()->getId() === intval($request->query->get('filter_user'))
                ) {
                    $specificationQuery->withUser($retailUserProfile->getUser());
                    $sortingUser = $retailUserProfile->getUser()->getId();
                }
            }
            // Re enable softdeleteable filter.
            $this->em->getFilters()->enable('softdeleteable');
            $specificationQuery->withRetailer($retailer);
        } else {
            $filters->addChoice('opened', 'Opened', ['group' => 'my']);
            $filters->addChoice('finished', 'Finished', ['group' => 'my']);
            $specificationQuery->withUser($user);
        }


        $specification = new Specification();
        $specification->setCreator($user->getRetailerUserProfile());

        $form = $this->formFactory->create(new SpecificationType(), $specification, [
            'owner' => $user,
        ]);

        if ($request->query->has('filter')) {
            switch ($request->query->get('filter')) {
                case 'finished':
                    $specificationQuery->finished();
                    $sortingState = 'finished';
                    break;

                default:
                    $specificationQuery->opened();
                    $sortingState = 'opened';
            }
        }
        else {
            // By default show only opened specifications
            $specificationQuery->opened();
            $sortingState = 'opened';
        }

        $selectedSortingItem = empty($sortingUser)
            ? $sortingState
            : sprintf('%s__%s', $sortingState, $sortingUser);

        $filters->setSelectedItem($selectedSortingItem);

        /* Create product paginator */
        $currentPage = (int)$request->get('page', 1);
        $specifications = $this->specificationRepository->findBy($specificationQuery);

        if ($specifications->getNbPages() < $currentPage) {
            $specifications->setCurrentPage($specifications->getNbPages());
        } else {
            $specifications->setCurrentPage($currentPage);
        }

        $content = $this->twig->render('FrontendBundle:Specification:specifications.html.twig', [
            'specifications' => $specifications,
            'filters'        => $filters,
            'form'           => $form->createView(),
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

        $buyer = null;

        if ($request->query->has('buyer')) {
            $buyerRepo = $this->em->getRepository(Buyer::class);
            $buyer = $buyerRepo->find($request->query->get('buyer'));
            if ($buyer) {
                $specification->setBuyer($buyer);
            }
        }

        $form = $this->formFactory->create(new SpecificationType(), $specification, [
            'owner' => $user,
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->em->persist($specification);
            $this->em->flush();

            $params = [];
            if ($buyer) {
                $route = 'specification_buyer_specifications';
                $params['buyer'] = $buyer->getId();
            } else {
                $route = 'specifications';
            }

            $url = $this->urlGenerator->generate($route, $params);

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

        $filters = new SimpleChoiceList(['all' => 'All'], ['selected' => 'all']);

        // Group items
        $groupedItemsByFactory = $specification->getGroupedVariantItemsByFactory();
        $groupedCustomItemsByFactory = $specification->getGroupedCustomItemsByFactory();

        foreach ($groupedItemsByFactory as $grouped) {
            $factory = $grouped->getFactory();
            $filters->addChoice($factory->getId(), $factory->getName(), ['group' => 'factory']);
        }

        foreach ($groupedCustomItemsByFactory as $grouped) {
            $factoryName = $grouped->getFactoryName();
            $filters->addChoice(md5($factoryName), $factoryName, ['group' => 'custom']);
        }

        $content = $this->twig->render('FrontendBundle:Specification/Export:preview.html.twig', [
            'specification' => $specification,
            'filters'       => $filters,
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
            '"'  => '',
            "\s" => '-',
        ];
        $name = strtr($name, $replaces);

        return $name . '.' . $ext;
    }
}
