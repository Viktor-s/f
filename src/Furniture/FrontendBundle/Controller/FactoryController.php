<?php

namespace Furniture\FrontendBundle\Controller;

use Furniture\FrontendBundle\Repository\FactoryRepository;
use Furniture\FrontendBundle\Repository\PostRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FactoryController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;
    
    /**
     * @var FactoryRepository
     */
    private $factoryRepository;

    /**
     * @var PostRepository
     */
    private $postRepository;
    
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * Construct
     *
     * @param \Twig_Environment     $twig
     * @param FactoryRepository     $factoryRepository
     * @param PostRepository        $postRepository
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        \Twig_Environment $twig,
        FactoryRepository $factoryRepository,
        PostRepository $postRepository,
        TokenStorageInterface $tokenStorage
    ){
        $this->twig = $twig;
        $this->factoryRepository = $factoryRepository;
        $this->postRepository = $postRepository;
        $this->tokenStorage = $tokenStorage;
    }
    
    public function factories(Request $request)
    {
        $factories = $this->factoryRepository->findAll();
        
        $content = $this->twig->render('FrontendBundle:FactorySide:factories.html.twig', [
            'factries' => $factories
        ]);

        return new Response($content);
    }
    
    public function general(Request $request, $factory)
    {
        $factory = $this->findFactory($factory);

        $content = $this->twig->render('FrontendBundle:FactorySide:general.html.twig', [
            'factory' => $factory
        ]);

        return new Response($content);
    }

    /**
     * View factory news
     *
     * @param int $factory
     *
     * @return Response
     */
    public function news($factory)
    {
        $factory = $this->findFactory($factory);

        $posts = $this->postRepository->findNewsForFactory($factory);

        $content = $this->twig->render('FrontendBundle:FactorySide:posts.html.twig', [
            'posts' => $posts,
            'factory' => $factory,
            'circulars' => false
        ]);

        return new Response($content);
    }

    /**
     * View factory circulars
     *
     * @param int $factory
     *
     * @return Response
     */
    public function circulars($factory)
    {
        $factory = $this->findFactory($factory);

        $posts = $this->postRepository->findCircularsForFactory($factory);

        $content = $this->twig->render('FrontendBundle:FactorySide:posts.html.twig', [
            'posts' => $posts,
            'factory' => $factory,
            'circulars' => true
        ]);

        return new Response($content);
    }

    /**
     * View post element
     *
     * @param int    $factory
     * @param string $slug
     *
     * @return Response
     */
    public function post($factory, $slug)
    {
        $factory = $this->findFactory($factory);
        $post = $this->postRepository->findBySlugForFactory($factory, $slug);

        if (!$post) {
            throw new NotFoundHttpException(sprintf(
                'Not found post with slug "%s" for factory "%s [%d]".',
                $slug,
                $factory->getName(),
                $factory->getId()
            ));
        }

        $content = $this->twig->render('FrontendBundle:FactorySide:post.html.twig', [
            'post' => $post,
            'factory' => $factory
        ]);

        return new Response($content);
    }

    /**
     * Find factory by identifier
     *
     * @param int $factory
     *
     * @return \Furniture\FactoryBundle\Entity\Factory
     *
     * @throws NotFoundHttpException
     */
    private function findFactory($factory)
    {
        $factory = $this->factoryRepository->find($factoryId = $factory);

        if (!$factory) {
            throw new NotFoundHttpException(sprintf(
                'Not found factory with identifier "%s".',
                $factoryId
            ));
        }

        return $factory;
    }
}
