<?php

namespace Furniture\FrontendBundle\Controller\Profile\Factory;

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

class UserRelationController
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
     * View user relations
     *
     * @return Response
     */
    public function userRelations()
    {
        $user = $this->tokenStorage->getToken()
            ->getUser();

        $userRequests = $this->factoryUserRelationRepository->findUserRequestsForFactory($user);
        $requestsToUsers = $this->factoryUserRelationRepository->findRequestToUsersForFactory($user);
        $relations = $this->factoryUserRelationRepository->findAuthorizedForFactory($user);

        $content = $this->twig->render('FrontendBundle:Profile/Factory/UserRelation:relations.html.twig', [
            'user_requests' => $userRequests,
            'requests_to_users' => $requestsToUsers,
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
        /** @var \Furniture\CommonBundle\Entity\User $user */
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
                ->setFactory($user->getFactory())
                ->setUserAccept(false)
                ->setFactoryAccept(true);
        }

        $form = $this->formFactory->create(new FactoryUserRelationType(), $relation, [
            'mode'         => 'from_factory',
            'factory_user' => $user
        ]);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->em->persist($relation);
            $this->em->flush();

            $url = $this->urlGenerator->generate('factory_profile_user_relations');

            return new RedirectResponse($url);
        }

        $content = $this->twig->render('FrontendBundle:Profile/Factory/UserRelation:edit.html.twig', [
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

        $relation->setFactoryAccept(true);

        $this->em->flush();

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(['status' => true]);
        }

        if ($request->get('_from')) {
            $url = $request->get('_from');

            return new RedirectResponse($url);
        }

        $url = $this->urlGenerator->generate('factory_profile_user_relations');

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

        $url = $this->urlGenerator->generate('factory_profile_user_relations');

        return new RedirectResponse($url);
    }
}
