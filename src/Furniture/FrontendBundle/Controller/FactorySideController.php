<?php

namespace Furniture\FrontendBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Furniture\FrontendBundle\Repository\FactoryRepository;
use Furniture\FrontendBundle\Repository\PostRepository;
use Furniture\FrontendBundle\Repository\Query\FactoryQuery;
use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonomyRepository;
use Sylius\Bundle\TaxonomyBundle\Doctrine\ORM\TaxonRepository;
use Sylius\Component\Core\Model\Taxon;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FactorySideController
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
     * @var TaxonomyRepository
     */
    private $taxonomyRepository;

    /**
     * @var TaxonRepository
     */
    private $taxonRepository;
    
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
     * @param TaxonomyRepository    $taxonomyRepository
     * @param TaxonRepository       $taxonRepository
     * @param TokenStorageInterface $tokenStorage
     */
    public function __construct(
        \Twig_Environment $twig,
        FactoryRepository $factoryRepository,
        PostRepository $postRepository,
        TaxonomyRepository $taxonomyRepository,
        TaxonRepository $taxonRepository,
        TokenStorageInterface $tokenStorage
    ){
        $this->twig = $twig;
        $this->factoryRepository = $factoryRepository;
        $this->postRepository = $postRepository;
        $this->taxonomyRepository = $taxonomyRepository;
        $this->taxonRepository = $taxonRepository;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * View factories
     *
     * @param Request $request
     *
     * @return Response
     */
    public function factories(Request $request)
    {
        $styles = $this->getStyles();
        $categories = $this->getCategories();

        // Resolve selected style
        $selectedStyle = null;

        if ($request->query->has('style')) {
            foreach ($styles as $style) {
                if ($style->getId() == $request->query->get('style')) {
                    $selectedStyle = $style;
                    break;
                }
            }
        }

        // Resolve selected category
        $selectedCategory = null;

        if ($request->query->has('category')) {
            foreach ($categories as $category) {
                if ($category->getId() == $request->query->get('category')) {
                    $selectedCategory = $category;
                    break;
                }
            }
        }

        // Create and populate query for search factories
        $query = new FactoryQuery();

        if ($selectedStyle) {
            $query->withTaxon($selectedStyle);
        }

        if ($selectedCategory) {
            $query->withTaxon($selectedCategory);
        }

        $factories = $this->factoryRepository->findBy($query);
        
        $content = $this->twig->render('FrontendBundle:FactorySide:factories.html.twig', [
            'factries' => $factories,
            'styles' => $styles,
            'selected_style' => $selectedStyle,
            'categories' => $categories,
            'selected_category' => $selectedCategory
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
     * View factory contacts
     *
     * @param int $factory
     *
     * @return Response
     */
    public function contacts($factory)
    {
        $factory = $this->findFactory($factory);

        $content = $this->twig->render('FrontendBundle:FactorySide:contacts.html.twig', [
            'factory' => $factory,
            'contacts' => $factory->getContacts()
        ]);

        return new Response($content);
    }

    /**
     * View work info
     *
     * @param int $factory
     *
     * @return Response
     */
    public function workInfo($factory)
    {
        $factory = $this->findFactory($factory);

        /** @var \Furniture\FactoryBundle\Entity\FactoryTranslation $translate */
        $translate = $factory->translate();

        $content = $this->twig->render('FrontendBundle:FactorySide:work_info.html.twig', [
            'factory' => $factory,
            'work_info' => $translate->getWorkInfoContent()
        ]);

        return new Response($content);
    }

    /**
     * View collections
     *
     * @param int $factory
     *
     * @return Response
     */
    public function collections($factory)
    {
        $factory = $this->findFactory($factory);

        /** @var \Furniture\FactoryBundle\Entity\FactoryTranslation $translate */
        $translate = $factory->translate();

        $content = $this->twig->render('FrontendBundle:FactorySide:collections.html.twig', [
            'factory' => $factory,
            'collection_content' => $translate->getCollectionContent()
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

    /**
     * Get styles
     *
     * @return array|\Sylius\Component\Core\Model\Taxon[]
     */
    private function getStyles()
    {
        /** @var \Sylius\Component\Core\Model\Taxonomy $styleTaxonomy */
        $styleTaxonomy = $this->taxonomyRepository->findOneBy([
            'name' => 'Style'
        ]);

        /** @var \Sylius\Component\Core\Model\Taxon[] $styles */
        $styles = $this->taxonRepository->findBy([
            'taxonomy' => $styleTaxonomy
        ]);

        // Filter style (Without root)
        $styles = array_filter($styles, function (Taxon $taxon) use ($styleTaxonomy) {
            return $taxon->getId() != $styleTaxonomy->getRoot()->getId();
        });

        return $styles;
    }

    /**
     * Get categories
     *
     * @return array|\Sylius\Component\Core\Model\Taxon[]
     */
    private function getCategories()
    {
        /** @var \Sylius\Component\Core\Model\Taxonomy $categoryTaxonomy */
        $categoryTaxonomy = $this->taxonomyRepository->findOneBy([
            'name' => 'Category'
        ]);

        $categories = $this->taxonRepository->findBy([
            'taxonomy' => $categoryTaxonomy,
            'parent' => $categoryTaxonomy->getRoot()
        ]);

        return $categories;
    }
}
