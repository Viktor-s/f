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
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class SecurityController
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
     * @param \Twig_Environment $twig
     * @param TokenStorageInterface $tokenStorage
     * @param CsrfTokenManagerInterface $csrfTokenManager
     * @param FormFactoryInterface $formFactory
     * @param UserRepository $userRepository
     * @param TranslatorInterface $translator
     * @param PasswordResetter $passwordResetter
     * @param UrlGeneratorInterface $urlGenerator
     * @param PasswordUpdater $passwordUpdater
     */
    public function __construct(
        \Twig_Environment $twig,
        TokenStorageInterface $tokenStorage,
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
        $this->tokenStorage = $tokenStorage;
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
            'error' => $error,
            'csrf_token' => $csrfToken,
        ]);

        return new Response($content);
    }

    /**
     * Need reset password
     *
     * @param Request $request
     *
     * @return Response
     */
    public function needResetPassword(Request $request)
    {
        if (!$request->server->has('HTTP_REFERER')) {
            $url = $this->urlGenerator->generate('security_login');

            return new RedirectResponse($url);
        }

        $content = $this->twig->render('FrontendBundle:Security:need_reset_password.html.twig');

        return new Response($content);
    }

    /**
     * Send reset password email.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function sendNeedResetPassword(Request $request)
    {
        $type = null;
        $message = null;
        $session = $request->getSession();
        $notSent = $this->translator->trans('frontend.reset_password_error');

        if ($session->has('reset-password-token')) {
            $token = $session->get('reset-password-token');
            // Try load user via token
            $user = $this->userRepository->findByConfirmationToken($token);

            if (!$user) {
                // User not found exception.
                $type = 'error';
                $message = sprintf(
                    '%s %s',
                    $notSent,
                    $this->translator->trans('frontend.messages.errors.user_not_found')
                );
            } else if ($user->isDisabled()) {
                // DisabledException;
                $type = 'error';
                $message = sprintf(
                    '%s %s',
                    $notSent,
                    $this->translator->trans('frontend.messages.errors.account_disabled')
                );
            } else {
                $this->passwordResetter->resetPassword($user);
                $token = $user->getConfirmationToken();
                $session->set('reset-password-token', $token);
                $type = 'success';
                $message = $this->translator->trans('frontend.messages.success.reset_password_success');
            }
        } else {
            // Session issue.
            $type = 'error';
            $message = sprintf(
                '%s %s',
                $notSent,
                $this->translator->trans('frontend.messages.errors.session_is_disabled')
            );
        }

        if ($type && $message) {
            $session->getFlashBag()->add($type, $message);
        }

        $url = $this->urlGenerator->generate('security_need_reset_password');

        return new RedirectResponse($url);
    }

    /**
     * Reset password process
     *
     * @param Request $request
     * @param string $token
     *
     * @return Response
     */
    public function resetPassword(Request $request, $token)
    {
        $session = $request->getSession();
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
                ':email' => $user->getUsername(),
            ]);
        }

        if ($error) {
            $content = $this->twig->render('FrontendBundle:Security:reset_password.html.twig', [
                'error' => $error,
                'form' => null,
            ]);

            return new Response($content);
        }

        $form = $this->formFactory->create('reset_password', ['user' => $user]);

        if ($request->getMethod() === Request::METHOD_POST) {
            $form->submit($request);

            if ($form->isValid()) {
                $session->set('reset-password-user', $user->getId());
                $formData = $form->getData();
                $password = $formData['password'];

                $user->resetPassword($password);
                $this->passwordUpdater->updatePassword($user);

                $this->em->flush();

                $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                $this->tokenStorage->setToken($token);
                // We should create a redirect response, because user can reload page and send repeatedly
                // send data.
                $url = $this->urlGenerator->generate('security_reset_password_success');

                return new RedirectResponse($url);
            }
        }

        $content = $this->twig->render('FrontendBundle:Security:reset_password.html.twig', [
            'form' => $form->createView(),
            'error' => $error,
        ]);

        return new Response($content);
    }

    /**
     * Success reset password
     *
     * @return Response
     */
    public function resetPasswordSuccessfully(Request $request)
    {
        if (!$request->server->has('HTTP_REFERER')) {
            $url = $this->urlGenerator->generate('security_login');

            return new RedirectResponse($url);
        }

        $session = $request->getSession();
        $content = $this->twig->render('FrontendBundle:Security:reset_password_success.html.twig');

        if ($session->has('reset-password-token')) {
            $session->remove('reset-password-token');
        }

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
        $type = null;
        $message = null;
        $session = $request->getSession();

        if ($request->getMethod() === Request::METHOD_POST) {
            $form->submit($request);

            if ($form->isValid()) {
                $formData = $form->getData();
                $email = $formData['email'];

                // Try search user by email
                /** @var \Furniture\UserBundle\Entity\User $user */
                $user = $this->userRepository->findByUsername($email);

                if (!$user) {
                    // User not found exception.
                    $type = 'error';
                    $message = sprintf(
                        '%s',
                        $this->translator->trans(
                            'frontend.user_not_found_with_email',
                            [
                                ':email' => $email,
                            ]
                        )
                    );
                } else if ($user->isDisabled()) {
                    $type = 'error';
                    $message = sprintf(
                        '%s',
                        $this->translator->trans(
                            'frontend.user_is_disabled_with_email',
                            [
                                ':email' => $email,
                            ]
                        )
                    );
                } else {
                    $token = $user->getConfirmationToken();
                    // Do  not send email if user already has confirmation token.
                    if (!$token) {
                        $this->passwordResetter->resetPassword($user);
                    }
                    $session->set('reset-password-token', $token);
                    // We should create a redirect response, because user can reload page and send repeatedly
                    // send data.
                    $url = $this->urlGenerator->generate('security_reset_password_requets_success');

                    return new RedirectResponse($url);
                }
            }
        }
        if ($type && $message) {
            $session->getFlashBag()->add($type, $message);
        }
        $content = $this->twig->render('FrontendBundle:Security:reset_password_request.html.twig', [
            'form' => $form->createView(),
            'reason' => $request->get('reason'),
        ]);

        return new Response($content);
    }

    /**
     * Reset password resend email.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function resetPasswordRequestReset(Request $request)
    {
        $type = null;
        $message = null;
        $session = $request->getSession();
        $notSent = $this->translator->trans('frontend.reset_password_error');
        if ($session->has('reset-password-token')) {
            $token = $session->get('reset-password-token');
            // Try load user via token
            $user = $this->userRepository->findByConfirmationToken($token);

            if (!$user) {
                // User not found exception.
                $type = 'error';
                $message = sprintf(
                    '%s %s',
                    $notSent,
                    $this->translator->trans('frontend.messages.errors.user_not_found')
                );
            } else if ($user->isDisabled()) {
                // DisabledException;
                $type = 'error';
                $message = sprintf(
                    '%s %s',
                    $notSent,
                    $this->translator->trans('frontend.messages.errors.account_disabled')
                );
            } else {
                $this->passwordResetter->resetPassword($user);
                $token = $user->getConfirmationToken();
                $session->set('reset-password-token', $token);
                $type = 'success';
                $message = $this->translator->trans('frontend.messages.success.reset_password_success');
            }
        } else {
            // Session issue.
            $type = 'error';
            $message = sprintf(
                '%s %s',
                $notSent,
                $this->translator->trans('frontend.messages.errors.session_is_disabled')
            );
        }

        if ($type && $message) {
            $session->getFlashBag()->add($type, $message);
        }

        $url = $this->urlGenerator->generate('security_reset_password_requets_success');

        return new RedirectResponse($url);
    }
    /**
     * Reset password successfully
     *
     * @param Request $request
     *
     * @return Response
     */
    public function resetPasswordRequestSuccessfully(Request $request)
    {
        if (!$request->server->has('HTTP_REFERER')) {
            $url = $this->urlGenerator->generate('security_login');

            return new RedirectResponse($url);
        }

        $content = $this->twig->render('FrontendBundle:Security:reset_password_request_success.html.twig');

        return new Response($content);
    }

    /**
     * Request to verify email address.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function verifyEmailRequest(Request $request, $token)
    {
        $user = $this->userRepository->findByVerifyEmailHashToken($token);
        if (!$user) {
            throw new NotFoundHttpException(sprintf(
                'Not found user with email verification token "%s".',
                $token
            ));
        }

        $user->setVerifyEmailHash(null);
        $this->em->persist($user);
        $this->em->flush();

        $session = $request->getSession();
        $session->set('email-verify', $user->getUsernameCanonical());
        $url = $this->urlGenerator->generate('security_verify_success');

        return new RedirectResponse($url);
    }

    /**
     * User successfully verify his email.
     *
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function verifyEmailSuccess(Request $request)
    {
        $content = '';
        $formView = null;
        $errorMessages = [];
        $session = $request->getSession();

        if ($session->has('email-verify')) {
            $userName = $session->get('email-verify');
            $user = $this->userRepository->findByUsername($userName);

            if ($user) {
                if ($user->isEnabled()) {
                    $form = $this->formFactory->create('reset_password', ['user' => $user]);
                    if ($request->getMethod() === Request::METHOD_POST) {
                        $form->handleRequest($request);
                        if ($form->isValid()) {
                            $formData = $form->getData();
                            $password = $formData['password'];

                            $user->resetPassword($password);
                            $this->passwordUpdater->updatePassword($user);

                            $this->em->flush();
                            $session->remove('email-verify');
                            $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
                            $this->tokenStorage->setToken($token);

                            return $this->resetPasswordSuccessfully($request);
                        }

                        $formView = $form->createView();
                    }
                } else {
                    $errorMessages[] = $this->translator->trans('frontend.messages.errors.account_disabled_contact_support');
                }
            } else {
                throw new NotFoundHttpException(sprintf(
                    'Not found user with username "%s".',
                    $userName
                ));
            }
        }
        else {
            $errorMessages[] = $this->translator->trans('frontend.messages.errors.session_is_disabled');
        }

        $content = $this->twig->render(
            'FrontendBundle:Security:email_verify_success.html.twig',[
            'form'           => $formView,
            'error_messgaes' => $errorMessages,
        ]);

        return new Response($content);
    }
}
