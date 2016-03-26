<?php

namespace Furniture\FrontendBundle\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Furniture\ProductBundle\Entity\Product;
use Sylius\Component\Translation\Provider\LocaleProviderInterface;

class SearchController
{
    /**
     * @var \Twig_Environment
     */
    private $twig;

    /**
     * @var UrlGeneratorInterface
     */
    private $urlGenerator;

    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var LocaleProviderInterface
     */
    private $localeProvider;

    /**
     * Construct
     *
     * @param \Twig_Environment       $twig
     * @param UrlGeneratorInterface   $urlGenerator
     * @param EntityManagerInterface  $em
     * @param TokenStorageInterface   $tokenStorage
     * @param LocaleProviderInterface $localeProvider
     */
    public function __construct(
        \Twig_Environment $twig,
        TokenStorageInterface $tokenStorage,
        EntityManagerInterface $em,
        UrlGeneratorInterface $urlGenerator,
        LocaleProviderInterface $localeProvider
    )
    {
        $this->twig = $twig;
        $this->tokenStorage = $tokenStorage;
        $this->em = $em;
        $this->urlGenerator = $urlGenerator;
        $this->localeProvider = $localeProvider;
    }

    public function index(Request $request)
    {
        $products = [];
        if ($request->query->has('s')) {
            $currentLocale = $this->localeProvider->getCurrentLocale();
            $languageMapping = [
                'en_US' => 'english',
                'en_GB' => 'english',
                'es_ES' => 'spanish',
                'de_DE' => 'german',
                'it_IT' => 'italian',
            ];
            $search = $request->query->get('s');
            $searchString = implode('|', str_word_count($search, 1));

            $qb = $this->em->createQueryBuilder()
                ->select('p')
                ->from(Product::class, 'p')
                ->join('p.translations', 'pt')
                ->andWhere("to_tsquery(pt.searchTsv, :search, :language) = true")
                ->andWhere('pt.locale = :locale')
                ->orderBy('ts_rank(pt.searchTsv, :search)', 'DESC')
                ->setParameter('search', $searchString)
                ->setParameter('language', $languageMapping[$currentLocale])
                ->setParameter('locale', $currentLocale);

            $products = $qb->getQuery()->getResult();
        }

        $content = $this->twig->render('FrontendBundle:Search:search_results.html.twig', [
            'products' => $products,
        ]);

        return new Response($content);
    }
}
