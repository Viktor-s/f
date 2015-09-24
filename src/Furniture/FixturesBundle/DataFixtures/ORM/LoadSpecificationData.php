<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Furniture\SpecificationBundle\Entity\Specification;

class LoadSpecificationData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        /** @var \Furniture\CommonBundle\Entity\User $administer */
        $administer = $this->getReference('Sylius.User-Administrator');

        $faker = Factory::create();

        for ($i = 0; $i < 5; $i++) {
            $hasBuyer = rand(0, 1);

            if ($hasBuyer) {
                $buyer = $this->getReference('specification:buyer:' . (string) rand(1, 5));
            } else {
                $buyer = null;
            }

            if (rand(0, 1)) {
                $description = $faker->text();
            } else {
                $description = null;
            }

            $specification = new Specification();
            $specification
                ->setUser($administer)
                ->setBuyer($buyer)
                ->setName($faker->words(rand(2, 4), true))
                ->setDescription($description);

            $manager->persist($specification);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 40;
    }
}
