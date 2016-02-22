<?php

namespace Furniture\FrontendBundle\Controller\Profile\Retailer;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\UserBundle\Entity\User;
use Furniture\RetailerBundle\Entity\RetailerUserProfile;
use Furniture\FrontendBundle\Repository\RetailerEmployeeRepository;
use Furniture\UserBundle\Security\EmailVerifier\EmailVerifier;
use Sylius\Component\User\Security\PasswordUpdater;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Furniture\UserBundle\Entity\Customer;

class EmployeeController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var RetailerEmployeeRepository
     */
    private $retailerEmployeeRepository;

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
     * @var PasswordUpdater
     */
    private $passwordUpdater;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var EmailVerifier
     */
    private $emailVerifier;

    /**
     * Construct
     *
     * @param \Twig_Environment             $twig
     * @param RetailerEmployeeRepository    $retailerEmployeeRepository
     * @param EntityManagerInterface        $em
     * @param TokenStorageInterface         $tokenStorage
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param FormFactoryInterface          $formFactory
     * @param PasswordUpdater               $passwordUpdater
     * @param UrlGeneratorInterface         $urlGenerator
     */
    public function __construct(
        \Twig_Environment $twig,
        RetailerEmployeeRepository $retailerEmployeeRepository,
        EntityManagerInterface $em,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        FormFactoryInterface $formFactory,
        PasswordUpdater $passwordUpdater,
        UrlGeneratorInterface $urlGenerator,
        EmailVerifier $emailVerifier
    ) {
        $this->twig = $twig;
        $this->retailerEmployeeRepository = $retailerEmployeeRepository;
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->formFactory = $formFactory;
        $this->passwordUpdater = $passwordUpdater;
        $this->urlGenerator = $urlGenerator;
        $this->emailVerifier = $emailVerifier;
    }

    /**
     * View all employees
     *
     * @return Response
     *
     * @throws AccessDeniedException
     */
    public function employees()
    {
        if (!$this->authorizationChecker->isGranted('RETAILER_EMPLOYEE_LIST')) {
            throw new AccessDeniedException();
        }

        /** @var \Furniture\UserBundle\Entity\User $user */
        $user = $this->tokenStorage->getToken()->getUser();
        $retailerProfile = $user->getRetailerUserProfile()->getRetailerProfile();

        $employees = $this->retailerEmployeeRepository->findForRetailer($retailerProfile);

        $content = $this->twig->render(
            'FrontendBundle:Profile/Retailer/Employee:list.html.twig',
            [
                'employees' => $employees,
            ]
        );

        return new Response($content);
    }

    /**
     * Create/Edit employee
     *
     * @param Request $request
     * @param int     $employee
     *
     * @return Response|RedirectResponse
     *
     * @throws AccessDeniedException
     */
    public function edit(Request $request, $employee = null)
    {
        /** @var User $user */
        $user = $this->tokenStorage->getToken()->getUser();

        if ($employee) {
            $employee = $this->retailerEmployeeRepository->find($employeeId = $employee);

            if (!$employee) {
                throw new NotFoundHttpException(
                    sprintf(
                        'Not found employee with id "%s".',
                        $employeeId
                    )
                );
            }

            if (!$this->authorizationChecker->isGranted('RETAILER_EMPLOYEE_EDIT', $employee)) {
                throw new AccessDeniedException(
                    sprintf(
                        'The active user "%s" not have rights for edit employee "%s".',
                        $user->getUsername(),
                        $employee->getUsername()
                    )
                );
            }
        }
        else {
            if (!$this->authorizationChecker->isGranted('RETAILER_EMPLOYEE_CREATE')) {
                throw new AccessDeniedException(
                    sprintf(
                        'The active user "%s" not have rights for create employee.',
                        $user->getUsername()
                    )
                );
            }

            $employee = new User();
            $retailerUserProfile = new RetailerUserProfile();
            $retailerUserProfile->setRetailerProfile($user->getRetailerUserProfile()->getRetailerProfile());
            $employee->setRetailerUserProfile($retailerUserProfile);
            $customer = new Customer();
            $employee->setCustomer($customer);
            $employee->setEnabled(true);
        }

        $form = $this->formFactory->create('retailer_employee', $employee);
        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($employee->getPlainPassword()) {
                $this->passwordUpdater->updatePassword($employee);
            }

            if (!$employee->getId()) {
                $pass = md5(uniqid(mt_rand(), true));
                $employee->setPlainPassword($pass);
                // Verify email action for created users.
                $this->emailVerifier->verifyEmail($employee, false);
            }
            $this->em->persist($employee);
            $this->em->flush();
            $url = $this->urlGenerator->generate('retailer_profile_employees');

            return new RedirectResponse($url);
        }

        $content = $this->twig->render(
            'FrontendBundle:Profile/Retailer/Employee:edit.html.twig',
            [
                'employee' => $employee,
                'form'     => $form->createView(),
            ]
        );

        return new Response($content);
    }

    /**
     * Remove employee
     *
     * @param int $employee
     *
     * @return RedirectResponse
     */
    public function remove($employee)
    {
        $employee = $this->retailerEmployeeRepository->find($employeeId = $employee);

        if (!$employee) {
            throw new NotFoundHttpException(
                sprintf(
                    'Not found employee with id "%s".',
                    $employeeId
                )
            );
        }

        if (!$this->authorizationChecker->isGranted('RETAILER_EMPLOYEE_REMOVE', $employee)) {
            throw new AccessDeniedException(
                sprintf(
                    'The active user "%s" not have rights for remove employee "%s".',
                    $this->tokenStorage->getToken()->getUsername(),
                    $employee->getUsername()
                )
            );
        }
        $employee->setEnabled(false);
        //$this->em->remove($employee);
        $this->em->flush();

        $url = $this->urlGenerator->generate('retailer_profile_employees');

        return new RedirectResponse($url);
    }
}
