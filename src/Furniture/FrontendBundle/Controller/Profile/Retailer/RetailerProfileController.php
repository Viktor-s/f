<?php

namespace Furniture\FrontendBundle\Controller\Profile\Retailer;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\CommonBundle\Entity\User;
use Furniture\RetailerBundle\Entity\RetailerProfileLogoImage;
use Furniture\RetailerBundle\Form\Type\RetailerProfileType;
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

class RetailerProfileController
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
     * @var AuthorizationCheckerInterface
     */
    private $authorizationChecker;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var CacheManager
     */
    private $cacheManager;

    /**
     * Construct
     *
     * @param \Twig_Environment             $twig
     * @param EntityManagerInterface        $em
     * @param TokenStorageInterface         $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param FormFactoryInterface          $formFactory
     * @param UrlGeneratorInterface         $urlGenerator
     * @param CacheManager                  $cacheManager
     */
    public function __construct(
        \Twig_Environment $twig,
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        CacheManager $cacheManager
    )
    {
        $this->twig = $twig;
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->cacheManager = $cacheManager;
    }

    /**
     * Edit retailer profile
     *
     * @param Request $request
     *
     * @return Response
     */
    public function edit(Request $request)
    {
        // Get active retailer profile
        $user = $this->tokenStorage->getToken()->getUser();

        if (!$user || !$user instanceof User) {
            throw new AccessDeniedException('Invalid user.');
        }

        if (!$user->isRetailer()) {
            throw new AccessDeniedException(sprintf(
                'The active user "%s" is no retailer.',
                $user->getUsername()
            ));
        }

        if (!$user->isRetailerAdmin()) {
            throw new AccessDeniedException(sprintf(
                'The active user "%s" is no admin.',
                $user->getUsername()
            ));
        }

        $profile = $user->getRetailerProfile();
        $form = $this->formFactory->create(new RetailerProfileType(), $profile);

        $form->handleRequest($request);

        if ($form->isValid()) {
            $this->em->persist($profile);
            $this->em->flush();

            $url = $this->urlGenerator->generate('retailer_profile');

            return new RedirectResponse($url);
        }

        $content = $this->twig->render('FrontendBundle:Profile/Retailer:profile_edit.html.twig', [
            'form' => $form->createView(),
        ]);

        return new Response($content);
    }

    /**
     * Upload logo for retailer
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function logoUpload(Request $request)
    {
        /** @var \Symfony\Component\HttpFoundation\File\UploadedFile $file */
        $file = $request->files->get('file');

        if (!$file) {
            throw new NotFoundHttpException('Not found "file" in FILES.');
        }

        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $retailer = $user->getRetailerProfile();

        if (!$retailer) {
            throw new NotFoundHttpException(sprintf(
                'Not found retailer profile for user "%s".',
                $user->getUsername()
            ));
        }

        if (!$this->authorizationChecker->isGranted('RETAILER_EDIT', $retailer)) {
            throw new AccessDeniedException(sprintf(
                'The active user "%s" not have rights for edit retailer profile "%d".',
                $this->tokenStorage->getToken()->getUsername(),
                $retailer->getId()
            ));
        }

        $image = $retailer->getLogoImage();

        if (!$image) {
            $image = new RetailerProfileLogoImage();
            $image->setRetailerProfile($retailer);
        }

        $image->setPath(null);
        $image->setFile($file);

        // @todo: validate file

        $this->em->persist($image);
        $this->em->flush();

        $path = $image->getPath();
        $path = $this->cacheManager->getBrowserPath($path, 's200x200');

        return new JsonResponse([
            'image' => $path,
            'status' => true
        ]);
    }

    /**
     * Remove logo
     *
     * @return JsonResponse
     */
    public function logoRemove()
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $retailer = $user->getRetailerProfile();

        if (!$retailer) {
            throw new NotFoundHttpException(sprintf(
                'Not found retailer profile for user "%s".',
                $user->getUsername()
            ));
        }

        if (!$this->authorizationChecker->isGranted('RETAILER_EDIT', $retailer)) {
            throw new AccessDeniedException(sprintf(
                'The active user "%s" not have rights for edit retailer profile "%d".',
                $this->tokenStorage->getToken()->getUsername(),
                $retailer->getId()
            ));
        }

        $image = $retailer->removeLogoImage();
        $this->em->remove($image);
        $this->em->flush();

        return new JsonResponse([
            'status' => true
        ]);
    }
}
