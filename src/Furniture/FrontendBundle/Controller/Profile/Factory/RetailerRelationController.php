<?php

namespace Furniture\FrontendBundle\Controller\Profile\Factory;

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

class RetailerRelationController
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
     * View user relations
     *
     * @return Response
     */
    public function retailerRelations()
    {
        /** @var \Furniture\UserBundle\Entity\User $user */
        $user = $this->tokenStorage->getToken()
            ->getUser();

        $retailerRequests = $this->factoryRetailerRelationRepository->findRetailerRequestsForFactory($user);
        $requestsToRetailers = $this->factoryRetailerRelationRepository->findRequestToRetailersForFactory($user);
        $relations = $this->factoryRetailerRelationRepository->findAuthorizedForFactory($user);

        $content = $this->twig->render('FrontendBundle:Profile/Factory/RetailerRelation:relations.html.twig', [
            'retailer_requests' => $retailerRequests,
            'requests_to_retailers' => $requestsToRetailers,
            'authorized_relations' => $relations
        ]);

        return new Response($content);
    }

    /**
     * Edit user relation
     *
     * @param Request $request
     * @param int     $relation
     *
     * @return Response
     */
    public function edit(Request $request, $relation = null)
    {
        /** @var \Furniture\UserBundle\Entity\User $user */
        $user = $this->tokenStorage->getToken()
            ->getUser();

        if (!$user->hasFactory()) {
            // @todo: control this error?
            throw new \RuntimeException(sprintf(
                'The user "%s" does not have a factory.',
                $user->getUsername()
            ));
        }

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
                ->setFactory($user->getFactory())
                ->setRetailerAccept(false)
                ->setFactoryAccept(true);
        }

        $form = $this->formFactory->create('retailer_factory_relation', $relation, [
            'mode'         => 'from_factory',
            'factory_user' => $user
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->em->persist($relation);
            $this->em->flush();

            $url = $this->urlGenerator->generate('factory_profile_retailer_relations');

            return new RedirectResponse($url);
        }

        $content = $this->twig->render('FrontendBundle:Profile/Factory/RetailerRelation:edit.html.twig', [
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

        $relation->setFactoryAccept(true);

        $this->em->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['status' => true]);
        }

        if ($request->get('_from')) {
            $url = $request->get('_from');

            return new RedirectResponse($url);
        }

        $url = $this->urlGenerator->generate('factory_profile_retailer_relations');

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

        $url = $this->urlGenerator->generate('factory_profile_retailer_relations');

        return new RedirectResponse($url);
    }
}
