<?php

namespace Furniture\FrontendBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FrontendBundle\Repository\UserRepository;
use Furniture\UserBundle\Form\Type\UserResetPasswordRequestType;
use Furniture\UserBundle\Form\Type\UserResetPasswordType;
use Furniture\UserBundle\PasswordResetter\PasswordResetter;
use Sylius\Component\User\Security\PasswordUpdater;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;

class SecurityController
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CsrfTokenManagerInterface
     */
    private $csrfTokenManager;

    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var PasswordResetter
     */
    private $passwordResetter;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var PasswordUpdater
     */
    private $passwordUpdater;

    /**
     * Construct
     *
     * @param \Twig_Environment         $twig
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param FormFactoryInterface      $formFactory
     * @param UserRepository            $userRepository
     * @param TranslatorInterface       $translator
     * @param PasswordResetter          $passwordResetter
     * @param UrlGeneratorInterface     $urlGenerator
     * @param PasswordUpdater           $passwordUpdater
     */
    public function __construct(
        \Twig_Environment $twig,
        EntityManagerInterface $em,
        CsrfTokenManagerInterface $csrfTokenManager,
        FormFactoryInterface $formFactory,
        UserRepository $userRepository,
        TranslatorInterface $translator,
        PasswordResetter $passwordResetter,
        UrlGeneratorInterface $urlGenerator,
        PasswordUpdater $passwordUpdater
    )
    {
        $this->em = $em;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->twig = $twig;
        $this->formFactory = $formFactory;
        $this->userRepository = $userRepository;
        $this->translator = $translator;
        $this->passwordResetter = $passwordResetter;
        $this->urlGenerator = $urlGenerator;
        $this->passwordUpdater = $passwordUpdater;
    }

    /**
     * Login action
     *
     * @param Request $request
     *
     * @return Response
     */
    public function login(Request $request)
    {
        /** @var $session \Symfony\Component\HttpFoundation\Session\Session */
        $session = $request->getSession();

        $authErrorKey = Security::AUTHENTICATION_ERROR;
        $lastUsernameKey = Security::LAST_USERNAME;

        // get the error if any (works with forward and redirect -- see below)
        if ($request->attributes->has($authErrorKey)) {
            $error = $request->attributes->get($authErrorKey);
        } elseif (null !== $session && $session->has($authErrorKey)) {
            $error = $session->get($authErrorKey);
            $session->remove($authErrorKey);
        } else {
            $error = null;
        }
        if (!$error instanceof AuthenticationException) {
            $error = null; // The value does not come from the security component.
        }
        // last username entered by the user
        $lastUsername = (null === $session) ? '' : $session->get($lastUsernameKey);

        $csrfToken = $this->csrfTokenManager
            ->getToken('authenticate')
            ->getValue();

        $content = $this->twig->render('FrontendBundle:Security:login.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
            'csrf_token'    => $csrfToken,
        ]);

        return new Response($content);
    }

    /**
     * Need reset password
     *
     * @return Response
     */
    public function needResetPassword()
    {
        $content = $this->twig->render('FrontendBundle:Security:need_reset_password.html.twig');

        return new Response($content);
    }

    /**
     * Reset password process
     *
     * @param Request $request
     * @param string  $token
     *
     * @return Response
     */
    public function resetPassword(Request $request, $token)
    {
        // Try load user via token
        $user = $this->userRepository->findByConfirmationToken($token);

        if (!$user) {
            throw new NotFoundHttpException(sprintf(
                'Not found user with confirmation token "%s".',
                $token
            ));
        }

        $error = null;

        if ($user->isDisabled()) {
            $error = $this->translator->trans('frontend.user_is_disabled_with_email', [
                'email' => $user->getUsername(),
            ]);
        }

        if ($error) {
            $content = $this->twig->render('FrontendBundle:Security:reset_password.html.twig', [
                'error' => $error,
                'form'  => null,
            ]);

            return new Response($content);
        }

        $form = $this->formFactory->create(new UserResetPasswordType());

        if ($request->getMethod() === Request::METHOD_POST) {
            $form->submit($request);

            if ($form->isValid()) {
                $formData = $form->getData();
                $password = $formData['password'];

                $user->resetPassword($password);
                $this->passwordUpdater->updatePassword($user);

                $this->em->flush();

                // We should create a redirect response, because user can reload page and send repeatedly
                // send data.
                $url = $this->urlGenerator->generate('security_reset_password_success');

                return new RedirectResponse($url);
            }
        }

        $content = $this->twig->render('FrontendBundle:Security:reset_password.html.twig', [
            'form'  => $form->createView(),
            'error' => null,
        ]);

        return new Response($content);
    }

    /**
     * Success reset password
     *
     * @return Response
     */
    public function resetPasswordSuccessfully()
    {
        $content = $this->twig->render('FrontendBundle:Security:reset_password_success.html.twig');

        return new Response($content);
    }

    /**
     * Request for reset password
     *
     * @param Request $request
     *
     * @return Response
     */
    public function resetPasswordRequest(Request $request)
    {
        $form = $this->formFactory->create(new UserResetPasswordRequestType());
        $error = null;

        if ($request->getMethod() === Request::METHOD_POST) {
            $form->submit($request);

            if ($form->isValid()) {
                $formData = $form->getData();
                $email = $formData['email'];

                // Try search user by email
                /** @var \Furniture\UserBundle\Entity\User $user */
                $user = $this->userRepository->findByUsername($email);

                if (!$user) {
                    $error = $this->translator->trans('frontend.user_not_found_with_email', [
                        ':email' => $email,
                    ]);
                } else if ($user->isDisabled()) {
                    $error = $this->translator->trans('frontend.user_is_disabled_with_email', [
                        ':email' => $email,
                    ]);
                }

                if (!$error) {
                    // Error not found. Start resetting password.
                    $this->passwordResetter->resetPassword($user);

                    // We should create a redirect response, because user can reload page and send repeatedly
                    // send data.
                    $url = $this->urlGenerator->generate('security_reset_password_requets_success');

                    return new RedirectResponse($url);
                }
            }
        }

        $content = $this->twig->render('FrontendBundle:Security:reset_password_request.html.twig', [
            'form'   => $form->createView(),
            'error'  => $error,
            'reason' => $request->get('reason'),
        ]);

        return new Response($content);
    }

    /**
     * Reset password successfully
     *
     * @return Response
     */
    public function resetPasswordRequestSuccessfully()
    {
        $content = $this->twig->render('FrontendBundle:Security:reset_password_request_success.html.twig');

        return new Response($content);
    }
}
