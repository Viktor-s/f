<?php

namespace Furniture\FrontendBundle\Controller\Profile\ContentUser;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FactoryBundle\Entity\UserFactoryRate;
use Furniture\FrontendBundle\Form\Type\UserFactoryRateType;
use Furniture\FrontendBundle\Repository\UserFactoryRateRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FactoryRatesController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var UserFactoryRateRepository
     */
    private $userFactoryRateRepository;

    /**
     * @var EntityManagerInterface
     */
    private $em;

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
     * @param \Twig_Environment         $twig
     * @param TokenStorageInterface     $tokenStorage
     * @param UserFactoryRateRepository $userFactoryRateRepository
     * @param EntityManagerInterface    $em
     * @param FormFactoryInterface      $formFactory
     * @param UrlGeneratorInterface     $urlGenerator
     */
    public function __construct(
        \Twig_Environment $twig,
        TokenStorageInterface $tokenStorage,
        UserFactoryRateRepository $userFactoryRateRepository,
        EntityManagerInterface $em,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator
    ) {
        $this->twig = $twig;
        $this->tokenStorage = $tokenStorage;
        $this->userFactoryRateRepository = $userFactoryRateRepository;
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Factory rates
     *
     * @return Response
     */
    public function rates()
    {
        $user = $this->tokenStorage->getToken()
            ->getUser();

        $rates = $this->userFactoryRateRepository->findByUser($user);

        $content = $this->twig->render('FrontendBundle:Profile/ContentUser/FactoryRate:list.html.twig', [
            'rates' => $rates
        ]);

        return new Response($content);
    }

    /**
     * Edit rate
     *
     * @param Request $request
     * @param int     $rate
     *
     * @return Response
     *
     * @throws NotFoundHttpException
     */
    public function edit(Request $request, $rate = null)
    {
        $user = $this->tokenStorage->getToken()
            ->getUser();

        if ($rate) {
            $rate = $this->userFactoryRateRepository->find($rateId = $rate);

            if (!$rate) {
                throw new NotFoundHttpException(sprintf(
                    'Not found factory rate with identifier "%s".',
                    $rateId
                ));
            }

            // @todo: check granted for edit this rate (via security voter in Symfony 2)
        } else {
            $rate = new UserFactoryRate();
            $rate->setUser($user);
        }

        $form = $this->formFactory->create(new UserFactoryRateType(), $rate);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->em->persist($rate);
            $this->em->flush();

            if ($request->get('_from')) {
                return new RedirectResponse($request->get('_from'));
            }

            $url = $this->urlGenerator->generate('content_user_profile_factory_rates');

            return new RedirectResponse($url);
        }

        $content = $this->twig->render('FrontendBundle:Profile/ContentUser/FactoryRate:edit.html.twig', [
            'rate' => $rate,
            'form' => $form->createView()
        ]);

        return new Response($content);
    }

    /**
     * Remove rate
     *
     * @param Request $request
     * @param int     $rate
     *
     * @return RedirectResponse
     */
    public function remove(Request $request, $rate)
    {
        $rate = $this->userFactoryRateRepository->find($rateId = $rate);

        if (!$rate) {
            throw new NotFoundHttpException(sprintf(
                'Not found user factory rate with identifier "%d".',
                $rateId
            ));
        }

        // @todo: add check grated for remove (via security voter in Symfony2)

        $this->em->remove($rate);
        $this->em->flush();

        if ($request->get('_from')) {
            return new RedirectResponse($request->get('_from'));
        }

        $url = $this->urlGenerator->generate('content_user_profile_factory_rates');

        return new RedirectResponse($url);
    }
}
