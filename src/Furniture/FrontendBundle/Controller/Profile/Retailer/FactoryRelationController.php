<?php

namespace Furniture\FrontendBundle\Controller\Profile\Retailer;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FactoryBundle\Entity\FactoryRetailerRelation;
use Furniture\FrontendBundle\Form\Type\FactoryRetailerRelationType;
use Furniture\FrontendBundle\Repository\FactoryRetailerRelationRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FactoryRelationController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FactoryRetailerRelationRepository
     */
    private $factoryRetailerRelationRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * Construct
     *
     * @param \Twig_Environment                 $twig
     * @param FactoryRetailerRelationRepository $factoryRetailerRelationRepository
     * @param EntityManagerInterface            $entityManager
     * @param TokenStorageInterface             $tokenStorage
     * @param UrlGeneratorInterface             $urlGenerator
     * @param FormFactoryInterface              $formFactory
     */
    public function __construct(
        \Twig_Environment $twig,
        FactoryRetailerRelationRepository $factoryRetailerRelationRepository,
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage,
        UrlGeneratorInterface $urlGenerator,
        FormFactoryInterface $formFactory
    ) {
        $this->twig = $twig;
        $this->factoryRetailerRelationRepository = $factoryRetailerRelationRepository;
        $this->em = $entityManager;
        $this->tokenStorage = $tokenStorage;
        $this->urlGenerator = $urlGenerator;
        $this->formFactory = $formFactory;
    }

    /**
     * View factory relations
     *
     * @return Response
     */
    public function factoryRelations()
    {
        /** @var \Furniture\CommonBundle\Entity\User $user */
        $user = $this->tokenStorage->getToken()
            ->getUser();

        $retailer = $user->getRetailerProfile();

        $factoryRequests = $this->factoryRetailerRelationRepository->findFactoryRequestsForRetailer($retailer);
        $requestsToFactories = $this->factoryRetailerRelationRepository->findRequestsToFactoriesForRetailer($retailer);
        $relations = $this->factoryRetailerRelationRepository->findAuthorizedForRetailer($retailer);

        $content = $this->twig->render('FrontendBundle:Profile/Retailer/FactoryRelation:relations.html.twig', [
            'factory_requests' => $factoryRequests,
            'requests_to_factories' => $requestsToFactories,
            'authorized_relations' => $relations
        ]);

        return new Response($content);
    }

    /**
     * Edit factory relation
     *
     * @param Request $request
     * @param int     $relation
     *
     * @return Response
     */
    public function edit(Request $request, $relation = null)
    {
        /** @var \Furniture\CommonBundle\Entity\User $user */
        $user = $this->tokenStorage->getToken()
            ->getUser();

        if ($relation) {
            $relation = $this->factoryRetailerRelationRepository->find($relationId = $relation);

            if (!$relation) {
                throw new NotFoundHttpException(sprintf(
                    'Not found relation with identifier "%d".',
                    $relationId
                ));
            }

            // @todo: add check granted for edit this relation (via security voter in Symfony2)
        } else {
            $relation = new FactoryRetailerRelation();
            $relation
                ->setRetailer($user->getRetailerProfile())
                ->setRetailerAccept(true)
                ->setFactoryAccept(false);
        }

        $form = $this->formFactory->create(new FactoryRetailerRelationType(), $relation, [
            'mode'         => 'from_retailer',
            'content_user' => $user
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->em->persist($relation);
            $this->em->flush();

            $url = $this->urlGenerator->generate('retailer_profile_factory_relations');

            return new RedirectResponse($url);
        }

        $content = $this->twig->render('FrontendBundle:Profile/Retailer/FactoryRelation:edit.html.twig', [
            'relation' => $relation,
            'form' => $form->createView()
        ]);

        return new Response($content);
    }

    /**
     * Approve relation
     *
     * @param Request $request
     * @param int     $relation
     *
     * @return Response
     */
    public function approve(Request $request, $relation)
    {
        $relation = $this->factoryRetailerRelationRepository->find($relationId = $relation);

        if (!$relation) {
            throw new NotFoundHttpException(sprintf(
                'Not found factory user relation with identifier "%d".',
                $relationId
            ));
        }

        // @todo: add check granted for approve this relation (via security voter in Symfony3)

        $relation->setRetailerAccept(true);

        $this->em->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['status' => true]);
        }

        if ($request->get('_from')) {
            $url = $request->get('_from');

            return new RedirectResponse($url);
        }

        $url = $this->urlGenerator->generate('retailer_profile_factory_relations');

        return new RedirectResponse($url);
    }

    /**
     * Remove user relation
     *
     * @param Request $request
     * @param int     $relation
     *
     * @return Response
     */
    public function remove(Request $request, $relation)
    {
        $relation = $this->factoryRetailerRelationRepository->find($relationId = $relation);

        if (!$relation) {
            throw new NotFoundHttpException(sprintf(
                'Not found factory user relation with identifier "%d".',
                $relationId
            ));
        }

        // @todo: add check granted for remove this relation (via security voter in Symfony2)

        $this->em->remove($relation);
        $this->em->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['status' => true]);
        }

        if ($request->get('_from')) {
            $url = $request->get('_from');

            return new RedirectResponse($url);
        }

        $url = $this->urlGenerator->generate('retailer_profile_factory_relations');

        return new RedirectResponse($url);
    }
}
