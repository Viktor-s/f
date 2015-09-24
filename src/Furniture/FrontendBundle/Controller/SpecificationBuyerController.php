<?php

namespace Furniture\FrontendBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FrontendBundle\Repository\SpecificationBuyerRepository;
use Furniture\SpecificationBundle\Entity\Buyer;
use Furniture\SpecificationBundle\Form\Type\BuyerType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class SpecificationBuyerController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var SpecificationBuyerRepository
     */
    private $buyerRepository;

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
     * @param \Twig_Environment            $twig
     * @param SpecificationBuyerRepository $buyerRepository
     * @param TokenStorageInterface        $tokenStorage
     * @param FormFactoryInterface         $formFactory
     * @param EntityManagerInterface       $em
     * @param UrlGeneratorInterface        $urlGenerator
     */
    public function __construct(
        \Twig_Environment $twig,
        SpecificationBuyerRepository $buyerRepository,
        TokenStorageInterface $tokenStorage,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->twig = $twig;
        $this->buyerRepository = $buyerRepository;
        $this->tokenStorage = $tokenStorage;
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * List all buyers action
     *
     * @return Response
     */
    public function buyers()
    {
        $user = $this->tokenStorage->getToken()
            ->getUser();

        $buyers = $this->buyerRepository->findByUser($user);

        $content = $this->twig->render('FrontendBundle:Specification/Buyer:buyers.html.twig', [
            'buyers' => $buyers
        ]);

        return new Response($content);
    }

    /**
     * Create a new buyer
     *
     * @param Request $request
     * @param int     $buyer
     *
     * @return Response|RedirectResponse
     */
    public function edit(Request $request, $buyer = null)
    {
        $user = $this->tokenStorage->getToken()
            ->getUser();

        if ($buyer) {
            $buyer = $this->buyerRepository->find($buyerId = $buyer);

            if (!$buyer) {
                throw new NotFoundHttpException(sprintf(
                    'Not found buyer with id "%s".',
                    $buyerId
                ));
            }

            // @todo: add control granted for edit this buyer (via security voter in Symfony)
        } else {
            $buyer = new Buyer();
            $buyer->setCreator($user);
        }

        $form = $this->formFactory->create(new BuyerType(), $buyer);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->em->persist($buyer);
            $this->em->flush();

            $url = $this->urlGenerator->generate('specification_buyers');

            return new RedirectResponse($url);
        }

        $content = $this->twig->render('FrontendBundle:Specification/Buyer:edit.html.twig', [
            'form' => $form->createView(),
            'buyer' => $buyer
        ]);

        return new Response($content);
    }

    /**
     * Remove buyer
     *
     * @param int $buyer
     *
     * @return RedirectResponse
     */
    public function remove($buyer)
    {
        $buyer = $this->buyerRepository->find($buyerId = $buyer);

        if (!$buyer) {
            throw new NotFoundHttpException(sprintf(
                'Not found buyer with idenfier "%s".',
                $buyerId
            ));
        }

        // @todo: add control granted for remove this buyer (via security voter in Symfony)

        $this->em->remove($buyer);
        $this->em->flush();

        $url = $this->urlGenerator->generate('specification_buyers');

        return new RedirectResponse($url);
    }
}
