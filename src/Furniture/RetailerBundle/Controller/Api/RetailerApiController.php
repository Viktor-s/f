<?php

namespace Furniture\RetailerBundle\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Furniture\RetailerBundle\Entity\RetailerProfile;
use Furniture\UserBundle\Entity\Customer;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class RetailerApiController
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
     * Check retailer name for existence.
     *
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function nameCheck(Request $request)
    {
        if ($request->request->has('name')) {
            $this->em->getFilters()->disable('softdeleteable');

            $retailerRepo = $this->em->getRepository(RetailerProfile::class);

            try {
                $retailerProfile = $retailerRepo->findOneByName($request->request->get('name'));
            } catch (NonUniqueResultException $e) {
                $retailerProfile = true;
            }

            return new JsonResponse((bool)!$retailerProfile);
        }

        $url = '/';
        if ($request->server->has('HTTP_REFERER')) {
            $url = $request->server->get('HTTP_REFERER');
        }

        return new RedirectResponse($url);
    }
}
