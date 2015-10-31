<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Furniture\ProductBundle\Entity\Readiness;

class LoadProductReadinessData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $position = 0;

        foreach (self::getReadiness() as $name) {
            $readiness = new Readiness();
            $readiness
                ->setName($name)
                ->setPosition($position++);

            $manager->persist($readiness);
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
     * Get readines
     *
     * @return array
     */
    public static function getReadiness()
    {
        return [
            'Ready',
            'Description',
            'Images',
            'Product parts',
            'SKU ready',
            'PDP settings',
            'Packaging',
            'Catalog price'
        ];
    }
}
