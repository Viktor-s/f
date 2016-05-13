<?php

namespace Furniture\UserBundle\Controller\Api;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\UserBundle\Entity\Customer;
use Furniture\UserBundle\Entity\User;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\TranslatorInterface;

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
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * UserApiController constructor.
     * @param EntityManagerInterface        $em
     * @param TokenStorageInterface         $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param UrlGeneratorInterface         $urlGenerator
     * @param TranslatorInterface $translator
     */
    public function __construct(
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        UrlGeneratorInterface $urlGenerator,
        TranslatorInterface $translator
    )
    {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->urlGenerator = $urlGenerator;
        $this->translator = $translator;
    }

    /**
     * Check that email not used.
     *
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    public function emailCheck(Request $request) {
        if ($request->request->has('email')) {
            $this->em->getFilters()->disable('softdeleteable');
            $customerRepo = $this->em->getRepository(Customer::class);
            $customer = $customerRepo->findOneBy(['email' => $request->request->get('email')]);
            
            $response = [
                'message' => ''
            ];
            
            if ($customer) {
                $response['status'] = false;
                $response['message'] = $this->translator->trans('frontend.email_already_used');
                /** @var User $user */
                $user = $customer->getUser();
                if (!empty($user->getVerifyEmailHash())) {
                    $response['message'] = $this->translator->trans('frontend.email_already_used_not_verified'); 
                }
            } else {
                $response['status'] = true;
            }

            return new JsonResponse($response);
        }

        $url = '/';
        if ($request->server->has('HTTP_REFERER')) {
            $url = $request->server->get('HTTP_REFERER');
        }

        return new RedirectResponse($url);
    }
}
