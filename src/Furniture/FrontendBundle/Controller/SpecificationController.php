<?php

namespace Furniture\FrontendBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FrontendBundle\Repository\SpecificationRepository;
use Furniture\FrontendBundle\Util\RedirectHelper;
use Furniture\SpecificationBundle\Entity\Specification;
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
     * Construct
     *
     * @param \Twig_Environment       $twig
     * @param SpecificationRepository $specificationRepository
     * @param TokenStorageInterface   $tokenStorage
     * @param FormFactoryInterface    $formFactory
     * @param EntityManagerInterface  $em
     * @param UrlGeneratorInterface   $urlGenerator
     */
    public function __construct(
        \Twig_Environment $twig,
        SpecificationRepository $specificationRepository,
        TokenStorageInterface $tokenStorage,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->twig = $twig;
        $this->specificationRepository = $specificationRepository;
        $this->tokenStorage = $tokenStorage;
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
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
}
