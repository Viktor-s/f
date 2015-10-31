<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Behat\Transliterator\Transliterator;
use Doctrine\Common\Persistence\ObjectManager;
use Furniture\ProductBundle\Entity\Space;
use Furniture\ProductBundle\Entity\SpaceTranslation;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;

class LoadProductSpaceData extends DataFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $position = 0;

        foreach (self::getSpaces() as $name) {
            $slug = Transliterator::transliterate($name);
            $space = new Space();
            $space
                ->setSlug($slug)
                ->setPosition($position++);

            $translation = new SpaceTranslation();
            $translation
                ->setName($name)
                ->setLocale($this->defaultLocale)
                ->setTranslatable($space);

            $space->addTranslation($translation);

            $manager->persist($space);

            $this->setReference('product.space:' . $name, $space);
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
     * Get spaces
     *
     * @return array
     */
    public static function getSpaces()
    {
        return [
            'Living Room',
            'Dining room',
            'Bedroom',
            'Workspace',
            'Bath'
        ];
    }
}
