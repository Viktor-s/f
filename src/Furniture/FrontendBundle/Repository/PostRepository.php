<?php

namespace Furniture\FrontendBundle\Repository;

use Doctrine\ORM\EntityManagerInterface;
use Furniture\FactoryBundle\Entity\Factory;
use Furniture\FrontendBundle\Repository\Query\PostQuery;
use Furniture\PostBundle\Entity\Post;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;

class PostRepository
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Construct
     *
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Find posts by query
     *
     * @param PostQuery $query
     * @param int       $page
     * @param int       $limit
     *
     * @return Post[]
     */
    public function findBy(PostQuery $query, $page = 1, $limit = 30)
    {
        $qb = $this->em->createQueryBuilder()
            ->from(Post::class, 'p')
            ->select('p');

        if ($query->hasFactories()) {
            $factoryIds = array_map(function (Factory $factory) {
                return $factory->getId();
            }, $query->getFactories());

            $qb
                ->innerJoin('p.factory', 'f')
                //If visible in front!
                ->andWhere('f.enabled = true')
                ->andWhere('f.id IN (:factory_ids)')
                ->setParameter('factory_ids', $factoryIds);
        }

        if ($query->hasTypes()) {
            $qb
                ->andWhere('p.type IN (:types)')
                ->setParameter('types', $query->getTypes());
        }

        if ($query->isOnlyPublished()) {
            $qb
                ->andWhere('p.publishedAt <= :now')
                ->setParameter('now', new \DateTime());
        }

        $qb->orderBy('p.publishedAt', 'DESC');

        if ($page === null) {
            return $qb
                ->getQuery()
                ->getResult();
        }

        $paginator = new Pagerfanta(new DoctrineORMAdapter($qb));
        $paginator->setCurrentPage($page);
        $paginator->setMaxPerPage($limit);

        return $paginator;
    }

    /**
     * Find news for factory
     *
     * @param Factory $factory
     * @param int     $page
     * @param int     $limit
     *
     * @return Post
     */
    public function findNewsForFactory(Factory $factory, $page = 1, $limit = 30)
    {
        $query = new PostQuery();
        $query
            ->withFactory($factory)
            ->withNews();

        return $this->findBy($query, $page, $limit);
    }

    /**
     * Find circulars for factory
     *
     * @param Factory $factory
     * @param int     $page
     * @param int     $limit
     *
     * @return Post
     */
    public function findCircularsForFactory(Factory $factory, $page = 1, $limit = 30)
    {
        $query = new PostQuery();
        $query
            ->withFactory($factory)
            ->withCircular();

        return $this->findBy($query, $page, $limit);
    }

    /**
     * Find post by slug for factory
     *
     * @param Factory $factory
     * @param string  $slug
     *
     * @return Post|null
     */
    public function findBySlugForFactory(Factory $factory, $slug)
    {
        return $this->em->createQueryBuilder()
            ->from(Post::class, 'p')
            ->select('p')
            ->andWhere('p.factory = :factory')
            ->andWhere('p.slug = :slug')
            ->setParameters([
                'factory' => $factory,
                'slug' => $slug
            ])
            ->getQuery()
            ->getOneOrNullResult();
    }
}
