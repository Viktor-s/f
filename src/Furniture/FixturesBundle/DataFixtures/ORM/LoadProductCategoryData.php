<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Behat\Transliterator\Transliterator;
use Doctrine\Common\Persistence\ObjectManager;
use Furniture\ProductBundle\Entity\Category;
use Furniture\ProductBundle\Entity\CategoryTranslation;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;

class LoadProductCategoryData extends DataFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $position = 0;

        foreach (self::getCategories() as $name) {
            $slug = Transliterator::transliterate($name);

            $category = new Category();
            $category
                ->setSlug($slug)
                ->setPosition($position++);

            $translation = new CategoryTranslation();
            $translation
                ->setName($name)
                ->setLocale($this->defaultLocale)
                ->setTranslatable($category);

            $category->addTranslation($translation);

            $manager->persist($category);

            $this->setReference('product.category:' . $name, $category);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 0;
    }

    /**
     * Get categories
     *
     * @return array
     */
    public static function getCategories()
    {
        return [
            'Upholstered',
            'Tables',
            'Seatings',
            'Storage',
            'Decor',
            'Lighting',
        ];
    }
}
