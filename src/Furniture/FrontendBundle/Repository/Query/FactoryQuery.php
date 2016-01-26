<?php

namespace Furniture\FrontendBundle\Repository\Query;

use Furniture\ProductBundle\Entity\Category;
use Furniture\ProductBundle\Entity\Style;
use Furniture\RetailerBundle\Entity\RetailerProfile;
use Furniture\UserBundle\Entity\User;
use Sylius\Component\Core\Model\Taxon;

class FactoryQuery
{
    /**
     * @var array
     */
    private $ids = [];

    /**
     * @var array|Style[]
     */
    private $styles = [];

    /**
     * @var array|Category[]
     */
    private $categories = [];

    /**
     * @var RetailerProfile
     */
    private $retailer;

    /**
     * With id
     *
     * @param int $id
     *
     * @return FactoryQuery
     */
    public function withId($id)
    {
        $this->ids[$id] = $id;

        return $this;
    }

    /**
     * With ids
     *
     * @param array $ids
     *
     * @return FactoryQuery
     */
    public function withIds(array $ids)
    {
        $this->ids = [];

        foreach ($ids as $id) {
            $this->withId($id);
        }

        return $this;
    }

    /**
     * Has ids?
     *
     * @return bool
     */
    public function hasIds()
    {
        return count($this->ids) > 0;
    }

    /**
     * Get ids
     *
     * @return array
     */
    public function getIds()
    {
        return array_values($this->ids);
    }

    /**
     * With style
     *
     * @param Style $style
     *
     * @return FactoryQuery
     */
    public function withStyle(Style $style)
    {
        if (!isset($this->styles[$style->getId()])) {
            $this->styles[$style->getId()] = $style;
        }

        return $this;
    }

    /**
     * With styles
     *
     * @param array|Style[] $styles
     *
     * @return FactoryQuery
     */
    public function withStyles(array $styles)
    {
        $this->styles = [];

        foreach ($styles as $taxon) {
            $this->withStyle($taxon);
        }

        return $this;
    }

    /**
     * Has styles?
     *
     * @return bool
     */
    public function hasStyles()
    {
        return count($this->styles) > 0;
    }

    /**
     * Get taxons
     *
     * @return array|Taxon[]
     */
    public function getStyles()
    {
        return array_values($this->styles);
    }

    /**
     * With category
     *
     * @param Category $category
     *
     * @return FactoryQuery
     */
    public function withCategory(Category $category)
    {
        $this->categories[$category->getId()] = $category;

        return $this;
    }

    /**
     * With categories
     *
     * @param array|Category[] $categories
     *
     * @return FactoryQuery
     */
    public function withCategories(array $categories)
    {
        $this->categories = [];

        foreach ($categories as $category) {
            $this->withCategory($category);
        }

        return $this;
    }

    /**
     * Has categories?
     *
     * @return bool
     */
    public function hasCategories()
    {
        return count($this->categories) > 0;
    }

    /**
     * Get categories
     *
     * @return Category[]
     */
    public function getCategories()
    {
        return array_values($this->categories);
    }

    /**
     * With retailer
     *
     * @param RetailerProfile $retailer
     *
     * @return FactoryQuery
     */
    public function withRetailer(RetailerProfile $retailer)
    {
        $this->retailer = $retailer;

        return $this;
    }

    /**
     * With retailer from user
     *
     * @param User $user
     *
     * @return FactoryQuery
     */
    public function withRetailerFromUser(User $user)
    {
        if ($user->getRetailerUserProfile()) {
            $userProfile = $user->getRetailerUserProfile();

            if ($userProfile->getRetailerProfile()) {
                $profile = $userProfile->getRetailerProfile();

                $this->withRetailer($profile);
            }
        }

        return $this;
    }

    /**
     * Has retailer?
     *
     * @return bool
     */
    public function hasRetailer()
    {
        return (bool) $this->retailer;
    }

    /**
     * Get retailer
     *
     * @return RetailerProfile
     */
    public function getRetailer()
    {
        return $this->retailer;
    }
}
