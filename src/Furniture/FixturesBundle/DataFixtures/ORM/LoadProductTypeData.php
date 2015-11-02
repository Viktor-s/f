<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Behat\Transliterator\Transliterator;
use Doctrine\Common\Persistence\ObjectManager;
use Furniture\ProductBundle\Entity\Type;
use Furniture\ProductBundle\Entity\TypeTranslation;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;

class LoadProductTypeData extends DataFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $position = 0;

        foreach (self::getTypes() as $name) {
            $slug = Transliterator::transliterate($name);
            $type = new Type();
            $type
                ->setSlug($slug)
                ->setPosition($position++);

            $translation = new TypeTranslation();
            $translation
                ->setName($name)
                ->setLocale($this->defaultLocale)
                ->setTranslatable($type);

            $type->addTranslation($translation);

            $manager->persist($type);

            $this->setReference('product.type:' . $name, $type);
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
     * Get product types
     *
     * @return array
     */
    public static function getTypes()
    {
        return [
            'Sofa',
            'Small sofa',
            'Armchair',
            'Easy chair',
            'Chaise longue',
            'Day bed',
            'Pouf',
            'Footstool',
            'Side Table',
            'Console table',
            'Fixed Table',
            'Extension Table',
            'Chair',
            'Chair with armrests',
            'Stool',
            'Bar/Counter stool',
            'Bench',
            'TV cabinet',
            'TV Stand',
            'Mount',
            'Bookcase',
            'Wall cabinet',
            'Storage wall',
            'High board',
            'Display Cabinet',
            'Side board',
            'Mirror',
            'China cabinet',
            'Bar cabinet',
            'Bed',
            'Headboard',
            'Night stand',
            'Dresser',
            'Wardrobe',
            'Dressing table',
            'Writing desk',
            'Secretary desk',
            'Chest of drawers',
            'Bathroom cabinet',
            'Coffee table'
        ];
    }
}
