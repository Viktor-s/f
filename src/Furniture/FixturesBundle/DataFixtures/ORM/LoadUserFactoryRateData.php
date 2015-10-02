<?php

namespace Furniture\FixturesBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Furniture\FactoryBundle\Entity\UserFactoryRate;

class LoadUserFactoryRateData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        /** @var \Furniture\CommonBundle\Entity\User $user */
        $user = $this->getReference('user:content:1');
        $factories = $this->getFactories();

        foreach ($factories as $factory) {
            $rate = new UserFactoryRate();
            $rate
                ->setUser($user)
                ->setFactory($factory)
                ->setCoefficient(1 + (rand(0, 20) / 100))
                ->setDumping(rand(0, 1) ? rand(1, 10) : 0);

            $manager->persist($rate);
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

    /**
     * Get factories
     *
     * @return array|\Furniture\FactoryBundle\Entity\Factory[]
     */
    private function getFactories()
    {
        return [
            $this->getReference('Furniture.factory.Selva'),
            $this->getReference('Furniture.factory.Antonello Italia'),
            $this->getReference('Furniture.factory.Kartell')
        ];
    }
}
