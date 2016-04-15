<?php

namespace Furniture\PostBundle\Entity\Repository;

use Sylius\Bundle\TranslationBundle\Doctrine\ORM\TranslatableResourceRepository;

class PostRepository extends TranslatableResourceRepository
{
    /**
     * Create filter paginator.
     *
     * @param array $criteria
     * @param array $sorting
     * @param bool  $deleted
     *
     * @return \Pagerfanta\PagerfantaInterface
     */
    public function createPaginator(array $criteria = [], array $sorting = [])
    {
        if (isset($criteria['useOnSlider'])) {
            switch ($criteria['useOnSlider']) {
                case 'slider':
                    $criteria['useOnSlider'] = true;
                    break;

                default:
                    unset($criteria['useOnSlider']);
                    break;
            }
        }

        $queryBuilder = $this->getCollectionQueryBuilder();

        $this->applyCriteria($queryBuilder, $criteria);
        $this->applySorting($queryBuilder, $sorting);

        return $this->getPaginator($queryBuilder);
    }
}
