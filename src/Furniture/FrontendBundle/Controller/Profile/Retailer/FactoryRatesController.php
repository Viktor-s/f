<?php

namespace Furniture\FrontendBundle\Controller\Profile\Retailer;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FactoryBundle\Entity\RetailerFactoryRate;
use Furniture\FrontendBundle\Form\Type\RetailerFactoryRateType;
use Furniture\FrontendBundle\Repository\RetailerFactoryRateRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Translation\TranslatorInterface;

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
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var RetailerFactoryRateRepository
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
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * Construct
     *
     * @param \Twig_Environment             $twig
     * @param TokenStorageInterface         $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param RetailerFactoryRateRepository $userFactoryRateRepository
     * @param EntityManagerInterface        $em
     * @param FormFactoryInterface          $formFactory
     * @param UrlGeneratorInterface         $urlGenerator
     * @param TranslatorInterface           $translator
     */
    public function __construct(
        \Twig_Environment $twig,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        RetailerFactoryRateRepository $userFactoryRateRepository,
        EntityManagerInterface $em,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator
    ) {
        $this->twig = $twig;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->userFactoryRateRepository = $userFactoryRateRepository;
        $this->em = $em;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    /**
     * Factory rates
     *
     * @return Response
     */
    public function rates(Request $request)
    {
        if (!$this->authorizationChecker->isGranted('RETAILER_FACTORY_RATE_LIST')) {
            throw new AccessDeniedException();
        }

        $session = $request->getSession();

        /** @var \Furniture\UserBundle\Entity\User $user */
        $user = $this->tokenStorage->getToken()
            ->getUser();

        $retailerUserProfile = $user->getRetailerUserProfile();
        $retailerProfile = $retailerUserProfile->getRetailerProfile();

        $rates = $this->userFactoryRateRepository->findByRetailerUserProfile($retailerUserProfile);
        
        if (empty($rates)) {
            $session->getFlashBag()->add('info', $this->translator->trans('frontend.retailer_profile_side.rates.info_coefficient'));
            $session->getFlashBag()->add('info', $this->translator->trans('frontend.retailer_profile_side.rates.info_sale_price'));
        }

        $content = $this->twig->render('FrontendBundle:Profile/Retailer/FactoryRate:list.html.twig', [
            'rates' => $rates,
            'has_factories_for_create' => $this->userFactoryRateRepository->hasFactoriesForCreateCondition($retailerProfile)
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
        /** @var \Furniture\UserBundle\Entity\User $user */
        $user = $this->tokenStorage->getToken()
            ->getUser();

        $session = $request->getSession();

        if ($rate) {
            $rate = $this->userFactoryRateRepository->find($rateId = $rate);

            if (!$rate) {
                throw new NotFoundHttpException(sprintf(
                    'Not found factory rate with identifier "%s".',
                    $rateId
                ));
            }

            if (!$this->authorizationChecker->isGranted('RETAILER_FACTORY_RATE_EDIT', $rate)) {
                throw new AccessDeniedException();
            }

            if ($rate->getFactory()->isDisabled()) {
                throw new NotFoundHttpException(sprintf(
                    'Can not edit factory rate with identifier "%s", because the factory "%s" is disabled.',
                    $rate->getId(),
                    $rate->getFactory()->getName()
                ));
            }
        } else {
            $rate = new RetailerFactoryRate();
            $rate->setRetailer($user->getRetailerUserProfile()->getRetailerProfile());

            if (!$this->authorizationChecker->isGranted('RETAILER_FACTORY_RATE_EDIT', $rate)) {
                throw new AccessDeniedException();
            }
        }

        $form = $this->formFactory->create('retailer_factory_rate', $rate);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->em->persist($rate);
            $this->em->flush();

            if ($request->get('_from')) {
                return new RedirectResponse($request->get('_from'));
            }

            $url = $this->urlGenerator->generate('retailer_profile_factory_rates');

            return new RedirectResponse($url);
        }

        $session->getFlashBag()->add('info', $this->translator->trans('frontend.retailer_profile_side.rates.info_calculations'));

        $content = $this->twig->render('FrontendBundle:Profile/Retailer/FactoryRate:edit.html.twig', [
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

        if (!$this->authorizationChecker->isGranted('RETAILER_FACTORY_RATE_REMOVE', $rate)) {
            throw new AccessDeniedException();
        }

        if ($rate->getFactory()->isDisabled()) {
            throw new NotFoundHttpException(sprintf(
                'Can not remove user factory rate with identifier "%s", because the factory "%s" is disabled.',
                $rate->getId(),
                $rate->getFactory()->getName()
            ));
        }

        $this->em->remove($rate);
        $this->em->flush();

        if ($request->get('_from')) {
            return new RedirectResponse($request->get('_from'));
        }

        $url = $this->urlGenerator->generate('retailer_profile_factory_rates');

        return new RedirectResponse($url);
    }
}
