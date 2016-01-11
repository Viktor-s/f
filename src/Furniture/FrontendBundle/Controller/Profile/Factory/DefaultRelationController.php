<?php

namespace Furniture\FrontendBundle\Controller\Profile\Factory;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FrontendBundle\Form\Type\FactoryDefaultRelationType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class DefaultRelationController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * Construct
     *
     * @param \Twig_Environment      $twig
     * @param EntityManagerInterface $em
     * @param TokenStorageInterface  $tokenStorage
     * @param UrlGeneratorInterface  $urlGenerator
     * @param FormFactoryInterface   $formFactory
     */
    public function __construct(
        \Twig_Environment $twig,
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        UrlGeneratorInterface $urlGenerator,
        FormFactoryInterface $formFactory
    ) {
        $this->twig = $twig;
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * View default relation for factory
     *
     * @return Response
     */
    public function defaultRelation()
    {
        /** @var \Furniture\UserBundle\Entity\User $user */
        $user = $this->tokenStorage->getToken()
            ->getUser();

        if (!$user->hasFactory()) {
            throw new \RuntimeException(sprintf(
                'The user "%s" does not have a factory.',
                $user->getUsername()
            ));
        }

        $factory = $user->getFactory();
        $defaultRelation = $factory->getDefaultRelation();

        $content = $this->twig->render('FrontendBundle:Profile/Factory/DefaultRelation:relation.html.twig', [
            'default_relation' => $defaultRelation
        ]);

        return new Response($content);
    }

    /**
     * Edit default relation for factory
     *
     * @param Request $request
     *
     * @return Response
     */
    public function edit(Request $request)
    {
        /** @var \Furniture\UserBundle\Entity\User $user */
        $user = $this->tokenStorage->getToken()
            ->getUser();

        if (!$user->hasFactory()) {
            throw new \RuntimeException(sprintf(
                'The user "%s" does not have a factory.',
                $user->getUsername()
            ));
        }

        $factory = $user->getFactory();
        $defaultRelation = $factory->getDefaultRelation();

        $form = $this->formFactory->create(new FactoryDefaultRelationType(), $defaultRelation);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->em->persist($defaultRelation);
            $this->em->flush();

            $url = $this->urlGenerator->generate('factory_profile_default_relation');

            return new RedirectResponse($url);
        }

        $content = $this->twig->render('FrontendBundle:Profile/Factory/DefaultRelation:edit.html.twig', [
            'form' => $form->createView()
        ]);

        return new Response($content);
    }
}
