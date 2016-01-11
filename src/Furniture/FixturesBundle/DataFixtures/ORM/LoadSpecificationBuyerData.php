<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use Furniture\SpecificationBundle\Entity\Buyer;

class LoadSpecificationBuyerData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create();

        /** @var \Furniture\UserBundle\Entity\User $administer */
        $administer = $this->getReference('Sylius.User-Administrator');
        $administer->getRetailerUserProfile()->getId();
        for ($i = 0; $i < 5; $i++) {
            $buyer = new Buyer();
            $buyer
                ->setCreator($administer->getRetailerUserProfile())
                ->setFirstName($faker->firstName)
                ->setSecondName($faker->lastName)
                ->setSale(rand(0, 10));

            $this->setReference('specification:buyer:' . ((string) ($i + 1)), $buyer);

            $manager->persist($buyer);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 30;
    }
}