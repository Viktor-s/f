<?php

namespace Furniture\FrontendBundle\Controller\Profile\User;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Furniture\CommonBundle\Entity\User;
use Symfony\Component\Form\FormFactoryInterface;
use Furniture\FrontendBundle\Form\Type\UserInformationType;
use Furniture\FrontendBundle\Form\Type\UserPasswordType;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Component\User\Security\PasswordUpdater;

class UserProfileController 
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
     * @var FormFactoryInterface
     */
    private $formFactory;
    
    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;
    
    /**
     * @var EntityManagerInterface
     */
    private $em;
    
    /**
     * @var PasswordUpdater
     */
    private $passwordUpdater;
    
    public function __construct(
        \Twig_Environment $twig,
        TokenStorageInterface $tokenStorage,
        AuthorizationCheckerInterface $authorizationChecker,
        FormFactoryInterface $formFactory,
        UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $em,
        PasswordUpdater $passwordUpdater
            ){
        $this->twig = $twig;
        $this->tokenStorage = $tokenStorage;
        $this->authorizationChecker = $authorizationChecker;
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->em = $em;
        $this->passwordUpdater = $passwordUpdater;
    }
    
    public function dashboard()
    {
        $content = $this->twig->render('FrontendBundle:Profile/User:dashboard.html.twig');
        return new Response($content);
    }
    
    public function updateInformation(Request $request)
    {
        // Get active user profile
        /* @var $user \Furniture\CommonBundle\Entity\User */
        $user = $this->tokenStorage->getToken()->getUser();
        
        if (!$user || !$user instanceof User) {
            throw new AccessDeniedException('Invalid user.');
        }
        
        $form = $this->formFactory->create(new UserInformationType(), $user);
        
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            $this->em->persist($user);
            $this->em->flush();

            $url = $this->urlGenerator->generate('user_profile');

            return new RedirectResponse($url);
        }
        
        $content = $this->twig->render('FrontendBundle:Profile/User:profile_edit.html.twig', [
            'form' => $form->createView(),
        ]);
        return new Response($content);
    }
    
    public function upatePassword(Request $request)
    {
        // Get active user profile
        /* @var $user \Furniture\CommonBundle\Entity\User */
        $user = $this->tokenStorage->getToken()->getUser();
        
        if (!$user || !$user instanceof User) {
            throw new AccessDeniedException('Invalid user.');
        }
        
        $form = $this->formFactory->create(new UserPasswordType(), $user);
        
        $form->handleRequest($request);
        
        if ($form->isValid()) {
            if ($user->getPlainPassword()) {
                $this->passwordUpdater->updatePassword($user);
            }
            $this->em->persist($user);
            $this->em->flush();

            $url = $this->urlGenerator->generate('user_profile');

            return new RedirectResponse($url);
        }
        
        $content = $this->twig->render('FrontendBundle:Profile/User:password_edit.html.twig', [
            'form' => $form->createView(),
        ]);
        return new Response($content);
    }
    
}

