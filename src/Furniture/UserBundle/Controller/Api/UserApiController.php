<?php

namespace Furniture\UserBundle\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\UserBundle\Entity\Customer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class UserApiController
{
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
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * UserApiController constructor.
     * @param EntityManagerInterface        $em
     * @param TokenStorageInterface         $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param UrlGeneratorInterface         $urlGenerator
     */
    public function __construct(
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        UrlGeneratorInterface $urlGenerator
    )
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * Check that email not used.
     *
     * @param Request $request
     */
    public function emailCheck(Request $request) {
        if ($request->request->has('email')) {
            $customerRepo = $this->em->getRepository(Customer::class);
            $customer = $customerRepo->findOneBy(['email' => $request->request->get('email')]);

            return new JsonResponse((bool) !$customer);
        }

        $url = '/';
        if ($request->server->has('HTTP_REFERER')) {
            $url = $request->server->get('HTTP_REFERER');
        }

        return new RedirectResponse($url);
    }
}
