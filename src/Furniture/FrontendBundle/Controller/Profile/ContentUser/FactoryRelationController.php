<?php

namespace Furniture\FrontendBundle\Controller\Profile\ContentUser;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FactoryBundle\Entity\FactoryUserRelation;
use Furniture\FrontendBundle\Form\Type\FactoryUserRelationType;
use Furniture\FrontendBundle\Repository\FactoryUserRelationRepository;
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
     * @var FactoryUserRelationRepository
     */
    private $factoryUserRelationRepository;

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
     * @param \Twig_Environment             $twig
     * @param FactoryUserRelationRepository $factoryUserRelationRepository
     * @param EntityManagerInterface        $entityManager
     * @param TokenStorageInterface         $tokenStorage
     * @param UrlGeneratorInterface         $urlGenerator
     * @param FormFactoryInterface          $formFactory
     */
    public function __construct(
        \Twig_Environment $twig,
        FactoryUserRelationRepository $factoryUserRelationRepository,
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage,
        UrlGeneratorInterface $urlGenerator,
        FormFactoryInterface $formFactory
    ) {
        $this->twig = $twig;
        $this->factoryUserRelationRepository = $factoryUserRelationRepository;
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
        $user = $this->tokenStorage->getToken()
            ->getUser();

        $factoryRequests = $this->factoryUserRelationRepository->findFactoryRequestsForUser($user);
        $requestsToFactories = $this->factoryUserRelationRepository->findRequestsToFactoriesForUser($user);
        $relations = $this->factoryUserRelationRepository->findAuthorizedForUser($user);

        $content = $this->twig->render('FrontendBundle:Profile/ContentUser/FactoryRelation:relations.html.twig', [
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
        $user = $this->tokenStorage->getToken()
            ->getUser();

        if ($relation) {
            $relation = $this->factoryUserRelationRepository->find($relationId = $relation);

            if (!$relation) {
                throw new NotFoundHttpException(sprintf(
                    'Not found relation with identifier "%d".',
                    $relationId
                ));
            }

            // @todo: add check granted for edit this relation (via security voter in Symfony2)
        } else {
            $relation = new FactoryUserRelation();
            $relation
                ->setUser($user)
                ->setUserAccept(true)
                ->setFactoryAccept(false);
        }

        $form = $this->formFactory->create(new FactoryUserRelationType(), $relation, [
            'mode' => 'from_user',
            'content_user' => $user
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->em->persist($relation);
            $this->em->flush();

            $url = $this->urlGenerator->generate('content_user_profile_factory_relations');

            return new RedirectResponse($url);
        }

        $content = $this->twig->render('FrontendBundle:Profile/ContentUser/FactoryRelation:edit.html.twig', [
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
        $relation = $this->factoryUserRelationRepository->find($relationId = $relation);

        if (!$relation) {
            throw new NotFoundHttpException(sprintf(
                'Not found factory user relation with identifier "%d".',
                $relationId
            ));
        }

        // @todo: add check granted for approve this relation (via security voter in Symfony3)

        $relation->setUserAccept(true);

        $this->em->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['status' => true]);
        }

        if ($request->get('_from')) {
            $url = $request->get('_from');

            return new RedirectResponse($url);
        }

        $url = $this->urlGenerator->generate('content_user_profile_factory_relations');

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
        $relation = $this->factoryUserRelationRepository->find($relationId = $relation);

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

        $url = $this->urlGenerator->generate('content_user_profile_factory_relations');

        return new RedirectResponse($url);
    }
}
