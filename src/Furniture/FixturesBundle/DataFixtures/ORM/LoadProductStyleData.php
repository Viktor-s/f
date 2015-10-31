<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Behat\Transliterator\Transliterator;
use Doctrine\Common\Persistence\ObjectManager;
use Furniture\ProductBundle\Entity\Style;
use Furniture\ProductBundle\Entity\StyleTranslation;
use Sylius\Bundle\FixturesBundle\DataFixtures\DataFixture;

class LoadProductStyleData extends DataFixture
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $position = 0;

        foreach (self::getStyles() as $name) {
            $slug = Transliterator::transliterate($name);

            $style = new Style();
            $style
                ->setSlug($slug)
                ->setPosition($position++);

            $translation = new StyleTranslation();
            $translation
                ->setName($name)
                ->setLocale($this->defaultLocale)
                ->setTranslatable($style);

            $style->addTranslation($translation);

            $manager->persist($style);

            $this->setReference('product.style:' . $name, $style);
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
     * Get styles
     *
     * @return array
     */
    public static function getStyles()
    {
        return [
            'Modern',
            'Contemporary',
            'Minimal',
            'Classic',
            'Traditional',
            'Scandinavian',
            'Provence',
            'French',
            'American',
            'Industrial',
            'English',
            'Art-Deco',
            'Baroque',
            'Shabby-Chic',
            'Rustic',
            'Mediterranean',
            'Asian',
            'Eclectic',
            'Vintage',
            'Fusion',
            'Retro'
        ];
    }
}
