<?php

namespace Furniture\FrontendBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\Util\ViolationListUtils;
use Furniture\FrontendBundle\Repository\SpecificationBuyerRepository;
use Furniture\SpecificationBundle\Entity\Buyer;
use Furniture\SpecificationBundle\Entity\BuyerImage;
use Furniture\SpecificationBundle\Form\Type\BuyerType;
use Liip\ImagineBundle\Imagine\Cache\CacheManager;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * Construct
     *
     * @param \Twig_Environment             $twig
     * @param SpecificationBuyerRepository  $buyerRepository
     * @param TokenStorageInterface         $tokenStorage
     * @param FormFactoryInterface          $formFactory
     * @param EntityManagerInterface        $em
     * @param UrlGeneratorInterface         $urlGenerator
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param CacheManager                  $cacheManager
     * @param ValidatorInterface            $validator
     */
    public function __construct(
        \Twig_Environment $twig,
        SpecificationBuyerRepository $buyerRepository,
        TokenStorageInterface $tokenStorage,
        FormFactoryInterface $formFactory,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        AuthorizationCheckerInterface $authorizationChecker,
        CacheManager $cacheManager,
        ValidatorInterface $validator
    )
    {
        $this->twig = $twig;
        $this->buyerRepository = $buyerRepository;
        $this->tokenStorage = $tokenStorage;
        $this->formFactory = $formFactory;
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
        $this->authorizationChecker = $authorizationChecker;
        $this->cacheManager = $cacheManager;
        $this->validator = $validator;
    }

    /**
     * List all buyers action
     *
     * @return Response
     */
    public function buyers()
    {
        if (!$this->authorizationChecker->isGranted('SPECIFICATION_BUYER_LIST')) {
            throw new AccessDeniedException();
        }

        /** @var \Furniture\CommonBundle\Entity\User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $retailer = $user->getRetailerUserProfile()->getRetailerProfile();

        $buyers = $this->buyerRepository->findByRetailer($retailer, true);
        //$buyersHasSpecifications = $this->buyerRepository->hasSpecificationsForBuyers($buyers, true);

        $content = $this->twig->render('FrontendBundle:Specification/Buyer:buyers.html.twig', [
            'buyers' => $buyers,
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
        /** @var \Furniture\CommonBundle\Entity\User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        if ($buyer) {
            $buyer = $this->buyerRepository->find($buyerId = $buyer);

            if (!$buyer) {
                throw new NotFoundHttpException(sprintf(
                    'Not found buyer with id "%s".',
                    $buyerId
                ));
            }

            if (!$this->authorizationChecker->isGranted('EDIT', $buyer)) {
                throw new AccessDeniedException(sprintf(
                    'The active user "%s" not have rights for edit buyer "%s [%d]".',
                    $this->tokenStorage->getToken()->getUsername(),
                    (string) $buyer,
                    $buyer->getId()
                ));
            }
        } else {
            $buyer = new Buyer();
            $buyer->setCreator($user->getRetailerUserProfile());

            if ($this->authorizationChecker->isGranted('SPECIFICATION_BUYER_CREATE')) {
                throw new AccessDeniedException(sprintf(
                    'The active user "%s" not have rights for create buyer.',
                    $this->tokenStorage->getToken()->getUsername()
                ));
            }
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
            'form'  => $form->createView(),
            'buyer' => $buyer,
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

        if (!$this->authorizationChecker->isGranted('REMOVE', $buyer)) {
            throw new AccessDeniedException(sprintf(
                'The active user "%s" not have rights for remove buyer "%s [%d]".',
                $this->tokenStorage->getToken()->getUsername(),
                (string) $buyer,
                $buyer->getId()
            ));
        }

        $this->em->remove($buyer);
        $this->em->flush();

        $url = $this->urlGenerator->generate('specification_buyers');

        return new RedirectResponse($url);
    }

    /**
     * Upload image for buyer
     *
     * @param Request $request
     * @param int     $buyer
     *
     * @return Response
     */
    public function imageUpload(Request $request, $buyer)
    {
        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $file = $request->files->get('file');

        if (!$file) {
            throw new NotFoundHttpException('Not found "file" in FILES.');
        }

        $buyer = $this->buyerRepository->find($buyerId = $buyer);

        if (!$buyer) {
            throw new NotFoundHttpException(sprintf(
                'Not found buyer with id "%s".',
                $buyerId
            ));
        }

        if (!$this->authorizationChecker->isGranted('EDIT', $buyer)) {
            throw new AccessDeniedException(sprintf(
                'The active user "%s" not have rights for edit buyer "%s [%d]".',
                $this->tokenStorage->getToken()->getUsername(),
                (string) $buyer,
                $buyer->getId()
            ));
        }

        $image = $buyer->getImage();

        if (!$image) {
            $image = new BuyerImage();
            $image->setBuyer($buyer);
            $buyer->setImage($image);
        }

        $image->setFile($file);

        $violations = $this->validator->validateProperty($image, 'file');

        if (count($violations)) {
            $errors = ViolationListUtils::convertToArrayWithoutPath($violations);

            return new JsonResponse([
                'status' => false,
                'errors' => $errors,
            ], 400);
        }

        $this->em->persist($image);
        $this->em->flush();

        $path = $image->getPath();
        $path = $this->cacheManager->getBrowserPath($path, 's100x100');

        return new JsonResponse([
            'image'  => $path,
            'status' => true,
        ]);
    }

    /**
     * Remove image
     *
     * @param int $buyer
     *
     * @return JsonResponse
     */
    public function imageRemove($buyer)
    {
        $buyer = $this->buyerRepository->find($buyerId = $buyer);

        if (!$buyer) {
            throw new NotFoundHttpException(sprintf(
                'Not found buyer with id "%s".',
                $buyerId
            ));
        }

        if (!$this->authorizationChecker->isGranted('EDIT', $buyer)) {
            throw new AccessDeniedException(sprintf(
                'The active user "%s" not have rights for edit buyer "%s [%d]".',
                $this->tokenStorage->getToken()->getUsername(),
                (string) $buyer,
                $buyer->getId()
            ));
        }

        $image = $buyer->removeImage();

        if ($image) {
            $this->em->remove($image);
            $this->em->flush();
        }

        return new JsonResponse([
            'status' => true,
        ]);
    }
}
